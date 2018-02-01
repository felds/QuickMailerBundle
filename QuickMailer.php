<?php

namespace Felds\QuickMailerBundle;

use Felds\QuickMailerBundle\Model\MailableInterface;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Twig_Environment;

class QuickMailer
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MailableInterface|null
     */
    private $from;
    /**
     * @var MailableInterface|null
     */
    private $replyTo;

    /**
     * @var \Twig_TemplateWrapper
     */
    private $template;

    /**
     * @var array
     */
    private $defaultData = [];

    /**
     * @var bool
     */
    private $isEnabled;

    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig, LoggerInterface $logger, string $template, bool $isEnabled = true)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->template = $this->twig->load($template);
        $this->isEnabled = $isEnabled;
    }

    /**
     * @TODO thow exception when a block is not found (maybe)
     */
    public function sendTo(MailableInterface $recipient, array $payload = []): int
    {
        if (!$this->from)
            throw new \RuntimeException("Please set the `from` field in the QuickMailer config.");

        if (!$this->isEnabled)
            return 0; // @TODO log

        $data = array_merge($this->defaultData, $payload);

        $subject    = $this->getSubject($data);
        $htmlBody   = $this->getHtmlBody($data);
        $textBody   = $this->getTextBody($data);

        /** @var \Swift_Message $message */
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
