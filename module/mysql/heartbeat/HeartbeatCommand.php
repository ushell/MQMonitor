<?php
namespace Monitor\module\mysql\heartbeat;

use Exception;
use Monitor\Core\Command;
use Monitor\Core\Job;
use Monitor\Common\Mail\Mail;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HeartbeatCommand extends Command
{
    public $name = 'mysql.heartbeat';

    public $args = [];

    private $jobId = 0;

    private $time = 2000;

    private function heartbeat()
    {
        $mail = new Mail();
        $mail->setSubject('xx')->setFrom(['228371630@qq.com'])->setTo('228371630@qq.com')->setBody('xxxx')->send();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->heartbeat();die;
        
        try {
            $this->jobId = Job::timer(function($input) {
                self::heartbeat($input);
            }, $this->time);
        } catch (Exception $e) {
            Job::clearTimer($this->jobId);

            $output->writeln(sprintf("Error: %s", $e->getMessage()));
        }
    }

}
