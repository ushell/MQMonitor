<?php
namespace Monitor\Common\Mail;

use Swift_Mailer;
use Swift_SendmailTransport;
use Swift_Message;
use Monitor\Core\Core;

class Mail
{
    private $instance = NULL;

    private $message = NULL;

    private $transport = '/usr/sbin/sendmail -bs';

    private $config = [];

    public function __construct()
    {
        if (! $this->instance)
        {
            $this->instance = new Swift_Mailer(new Swift_SendmailTransport($this->transport));
            $this->message = new Swift_Message();
        }

        if (! isset(Core::$config['mail']))
        {
            throw new \Exception('mail configure not found !');
        }

        $this->config = Core::$config['mail'];
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