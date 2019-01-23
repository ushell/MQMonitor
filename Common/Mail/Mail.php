<?php
namespace Monitor\Common\Mail;

use Swift_Mailer;
use Swift_Message;
use Swift_SendmailTransport;
use Monitor\Core\Core;

class Mail
{
    private $instance = NULL;

    private $message = NULL;

    private $transport = '/usr/sbin/sendmail -bs';

    private $config = [];

    public function __construct()
    {
        if (! isset(Core::$config['mail']))
        {
            throw new \Exception('mail configure not found !');
        }

        $this->config = Core::$config['mail'];

        if (! $this->instance)
        {
            $transport = new Swift_SendmailTransport();
            if ($this->config['is_smtp'])
            {
                $transport = new \Swift_SmtpTransport($this->config['smtp']['host'], $this->config['smtp']['port']);
                $transport->setUsername($this->config['smtp']['username']);
                $transport->setPassword($this->config['smtp']['password']);
                $transport->setEncryption($this->config['smtp']['encryption']);
            }

            $this->instance = new Swift_Mailer($transport);
            $this->message = new Swift_Message();
        }

    }

    public function setSubject($subject)
    {
        $this->message->setSubject($this->formatSubject($subject));

        return $this;
    }

    public function setBody($body)
    {
        $this->message->setBody($body);

        return $this;
    }

    public function setFrom($from = [])
    {
        if (empty($from))
        {
            $from = $this->config['default']['sender'];
        }

        $this->message->setFrom($from);

        return $this;
    }

    public function setTo($mail = [])
    {
        if (empty($mail))
        {
            $mail = $this->config['default']['email'];
        }

        $this->message->setTo($mail);

        return $this;
    }

    public function send()
    {
        return $this->instance->send($this->message);
    }

    private function formatSubject($subject)
    {
        return sprintf("[%s][%s]", $this->config['default']['title'], $subject);
    }

}