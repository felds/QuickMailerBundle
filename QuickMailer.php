<?php

namespace Felds\QuickMailerBundle;

use Felds\QuickMailerBundle\Model\Mailable;
use Felds\QuickMailerBundle\Model\MailableInterface;
use Swift_Mailer;
use Twig_Environment;

class QuickMailer
{
    private $mailer;
    private $twig;
    private $from;
    private $replyTo;
    private $template;
    private $defaultData = [];

    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig, string $template)
    {
        $this->mailer   = $mailer;
        $this->twig     = $twig;
        $this->template = $this->twig->load($template);
    }

    /**
     * @TODO thow exception when a block is not found (maybe)
     * @TODO validate from
     */
    public function sendTo(MailableInterface $recipient, array $payload = []): int
    {
        $data = array_merge($this->defaultData, $payload);

        $subject    = $this->getSubject($data);
        $htmlBody   = $this->getHtmlBody($data);
        $textBody   = $this->getTextBody($data);

        $message = $this->mailer->createMessage()
            ->setFrom([ $this->from->getEmail() => $this->from->getName() ])
            ->setTo([ $recipient->getEmail() => $recipient->getName() ])
            ->setSubject($subject)
            ->addPart($htmlBody, 'text/html')
            ->addPart($textBody, 'text/plain')
        ;

        if ($this->replyTo) {
            $message->setReplyTo([ $this->replyTo->getEmail() => $this->replyTo->getName() ]);
        }

        return $this->mailer->send($message);
    }

    public function setFrom(MailableInterface $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function setReplyTo(MailableInterface $replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function setDefaultData(array $data): self
    {
        $this->defaultData = $data;

        return $this;
    }

    private function getSubject(array $payload = []): string
    {
        return $this->template->renderBlock('subject', $payload);
    }

    private function getHtmlBody(array $payload = []): string
    {
        return $this->template->renderBlock('html', $payload);
    }

    private function getTextBody(array $payload = []): string
    {
        return $this->template->renderBlock('text', $payload);
    }
}
