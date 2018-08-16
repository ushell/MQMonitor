<?php
namespace Monitor\module\mysql\heartbeat;

use Exception;
use Monitor\Core\Command;
use Monitor\Core\Job;
use Monitor\Core\Core;
use Monitor\Common\Mail\Mail;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HeartbeatCommand extends Command
{
    public $name = 'mysql.heartbeat';

    public $description = 'MySQL心跳检测';

    public $args = [];

    private $jobId = 0;

    private $time = 5000;

    private $config;

    private $message = [];

    public function prepare()
    {
        if (! isset(Core::$config['mysql']))
        {
            throw new Exception('mysql configure not found !');
        }

        $this->config = Core::$config['mysql'];
    }

    /**
     * 数据库连接
     *
     * @param array $config
     * @return null|\PDO
     */
    private function dbConnectCheck($config = [])
    {
        $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s", $config['host'], $config['port'], $config['database']);

        try {
            $pdo = new \PDO($dsn, $config['username'], $config['password']);

            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $error = $e->getMessage();

            $this->message[] = sprintf("[%s] PDOException, %s", $config['host'], $error);

            $pdo = NULL;
        }

        return $pdo;
    }

    /**
     * 读写测试
     *
     * @param $handle
     * @param array $config
     */
    private function dbWriteStatus($handle, $config = [])
    {
        if (empty($handle))
        {
            return;
        }
        //查询SQL
        $readSQL = sprintf("SELECT * FROM %s LIMIT 1", $config['table']);

        //写SQL
        $writeSQL = sprintf("UPDATE %s SET updated_at=NOW() WHERE id=1", $config['table']);

        try {
            $obj = $handle->query($writeSQL);
        } catch (Exception $e) {
            $this->message[] = sprintf("[%s] UPDATE Error, SQL=%s, Error=%s", $config['host'], $writeSQL, $e->getMessage());

            return;
        }

        try {
            $obj = $handle->query($readSQL);
            $obj->fetchAll();
        } catch (Exception $e) {
            $this->message[] = sprintf("[%s] SELECT Error, SQL=%s, Error=%s", $config['host'], $readSQL, $e->getMessage());

            return;
        }

    }

    /**
     * 系统状态检测
     *
     * @param $handle
     * @param array $host
     */
    private function dbSysStatus($handle, $config = [])
    {
        if (empty($handle))
        {
            return;
        }

        $slaveStatusSQL     = "SHOW SLAVE STATUS";
        $processSQL         = "SHOW PROCESSLIST";

        try {
            $slaveObj   = $handle->query($slaveStatusSQL);
            $slave      = $slaveObj->fetchAll();
            //主从架构
            if ($slave)
            {
                if ($slave['Slave_IO_Running'] == 'No')
                {
                    $this->message[] = sprintf("[%s] SlaveMySQL SyncStop", $config['host']);
                }

                if ($slave['Slave_SQL_Running'] == 'No')
                {
                    $this->message[] = sprintf("[%s] SlaveMySQL SyncStop", $config['host']);
                }

                if ($slave['Last_IO_Errno'])
                {
                    $this->message[] = sprintf("[%s] SlaveMySQL IOError, IONo=%s, IOError=%s", $config['host'], $slave['Last_IO_Errno'], $slave['Last_IO_Error']);
                }
            }

            //系统线程状态
            $processObj = $handle->query($processSQL);
            $process    = $processObj->fetchAll();
            if ($process)
            {
                foreach ($process as $thread)
                {
                    if (false !== stripos($thread['State'], 'Lock') || false !== stripos($thread['State'], 'too many connection'))
                    {
                        $this->message[] = sprintf("[%s] DBException, Error=%s", $config['host'], json_encode($thread));
                    }
                }
            }

        } catch (Exception $e) {
            $this->message[] = sprintf("[%s] SELECT Error, Error=%s", $config['host'], $e->getMessage());
        }
    }

    /**
     * 心跳检测
     * 
     * @return [type] [description]
     */
    private function heartbeat()
    {
        foreach ($this->config as $host)
        {
            $handle = $this->dbConnectCheck($host);
            if ($handle)
            {
                $this->dbWriteStatus($handle, $host);

                $this->dbSysStatus($handle, $host);
            }
        }

        $this->notify();
    }

    /**
     * 执行命令
     * 
     * @param  InputInterface  $input  [description]
     * @param  OutputInterface $output [description]
     * @return [type]                  [description]
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->prepare();

            $this->jobId = Job::timer(function($input) {
                self::heartbeat($input);
            }, $this->time);
        } catch (Exception $e) {
            Job::clearTimer($this->jobId);

            $output->writeln(sprintf("Error: %s", $e->getMessage()));
        }
    }

    /**
     * 消息通知
     * 
     * @param  string $message [description]
     * @return [type]          [description]
     */
    private function notify($message = '')
    {
        $mail = new Mail();

        $message = empty($message) ? implode(PHP_EOL, $this->message) : $message;

        if ($message) {
            $mail->setSubject('数据库状态')->setFrom()->setTo()->setBody($message)->send();
        }

        $this->message = [];
    }

}
