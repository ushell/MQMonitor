<?php
namespace Monitor\Common\Mail;

use Swift_Mailer;
use Swift_SendmailTransport;
use Swift_Message;

class Mail
{
    private $instance = NULL;

    private $message = NULL;

    private $transport = '/usr/sbin/sendmail -bs';

    public function __construct()
    {
        if (! $this->instance)
        {
            $this->instance = new Swift_Mailer(new Swift_SendmailTransport($this->transport));
            $this->message = new Swift_Message();
        }
    }

    public function setSubject($subject)
    {
        $this->message->setSubject($subject);

        return $this;
    }

    public function setBody($body)
    {
        $this->message->setBody($body);

        return $this;
    }

    public function setFrom($from = [])
    {
        $this->message->setFrom($from);

        return $this;
    }

    public function setTo($mail)
    {
        $this->message->setTo($mail);

        return $this;
    }

    public function send()
    {
        return $this->instance->send($this->message);
    }
	
}