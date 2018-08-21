<?php
namespace Monitor\module\supervisor\monitor;

use Monitor\Core\Command;
use Monitor\Core\Job;
use Monitor\Core\Core;
use Monitor\Common\Mail\Mail;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MonitorCommand extends Command
{
    public $name = 'supervisor.monitor';

    public $description = 'Supervisor状态检测';

    public $args = [];

    private $jobId = 0;

    private $time = 60000;

    private $message = [];

    /**
     * 解析日志
     * 
     * @param  [type] $log    [description]
     * @param  string $offset [description]
     * @return [type]         [description]
     */
    private function parseLog($log, $offset = '')
    {
        $fd = fopen($log, 'r');
        if (! $fd)
        {
            return;
        }

        $message = [];

        while(! feof($fd))
        {
            $line = fgets($fd, 1024);

            $data = explode(',', $line);

            if ($offset && strtotime($data[0]) <= $offset)
            {
                continue;
            }

            if (strpos($line, 'ERRO') !== false || strpos($line, 'CRIT') !== false || strpos($line, 'WARN') !== false)
            {
                array_push($message, $line);
            }
        }

        return implode('', $message);
    }

    /**
     * 增量锁
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    private function lock($name)
    {
        $lock = sprintf("%s/runtime/supervisor/%s.lock", Core::$basePath, md5($name));

        $lastReadTime = $timestamp = time();
        if (file_exists($lock))
        {
            $lastReadTime = file_get_contents($lock);
        } else {
            mkdir(dirname($lock), 0755);
        }

        file_put_contents($lock, $timestamp);

        return $lastReadTime;
    }

    /**
     * 检查日志
     * @param  [type] $name [description]
     * @param  [type] $log  [description]
     * @return [type]       [description]
     */
    private function checkLog($name, $log)
    {
        if (! file_exists($log))
        {
            return sprintf("Log path %s not found", $log);
        }

        $lastReadTime = $this->lock($name);        

        return $this->parseLog($log, $lastReadTime);
    }

    /**
     * 循环监控
     * @param  array  $list [description]
     * @return [type]       [description]
     */
    private function checkStatusLoop($list = [])
    {
        $message = [];

        foreach ($list as $instance)
        {
            if (empty($instance['log']))
            {
                continue;
            }

            $error = $this->checkLog($instance['name'], $instance['log']);
            if (! empty($error))
            {
                $message[] = sprintf("[%s]\r\n%s", $instance['name'], $error);
            }
        }

        $message = implode('', $message);

        $this->notify($message);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty(Core::$config['supervisor']))
        {
            throw new \Exception("No Supervisor configure");
        }

        $supervisorList = Core::$config['supervisor'];

        try {
            $this->jobId = Job::timer(function($input) use ($supervisorList) {
                $this->checkStatusLoop($supervisorList);
            }, $this->time);
        } catch (\Exception $e) {
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
            $mail->setSubject('Supervisor状态')->setFrom()->setTo()->setBody($message)->send();
        }

        $this->message = [];
    }

}