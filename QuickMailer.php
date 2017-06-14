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
    private $template;

    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig, string $template)
    {
        $this->mailer   = $mailer;
        $this->twig     = $twig;
        $this->template = $this->twig->load($template);
    }

    /**
     * @TODO thow exception when a block is not found (maybe)
     */
    public function sendTo(MailableInterface $recipient, array $payload = []): int
    {
        $subject    = $this->getSubject($payload);
        $htmlBody   = $this->getHtmlBody($payload);
        $textBody   = $this->getTextBody($payload);

        $message = $this->mailer->createMessage()
            ->setFrom([ $this->from->getEmail() => $this->from->getName() ])
            ->setTo([ $recipient->getEmail() => $recipient->getName() ])
            ->setSubject($subject)
            ->addPart($htmlBody, 'text/html')
            ->addPart($textBody, 'text/plain')
        ;

        return $this->mailer->send($message);
    }

    public function setFrom(MailableInterface $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function setFromByNameAndEmail(string $name, string $email): self
    {
        $this->from = new Mailable($name, $email);

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
