<?php
namespace Monitor\module\service;

use Monitor\Core\Core;
use Monitor\Core\Command;
use Monitor\Common\Mail\Mail;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DemoCommand extends Command
{
    public $name = 'service.demo';

    public $description = '示例command描述信息';

    public $args = [];

    private $message = [];

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
             $this->notify("邮件通知");
        } catch (\Exception $e) {
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

        if ($message) 
        {
            $mail->setSubject('服务通知')->setFrom()->setTo()->setBody($message)->send();
        }

        $this->message = [];
    }

}
