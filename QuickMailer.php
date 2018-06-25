<?php

namespace Felds\QuickMailerBundle;

use Felds\QuickMailerBundle\Exception\TemplateNotFound;
use Felds\QuickMailerBundle\Model\MailableInterface;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Throwable;
use Twig_Environment;

class QuickMailer implements QuickMailerInterface
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
     * @var array<string, array> $templates
     */
    private $templates;




    /**
     * @param array<string, array> $templates
     */
    public function __construct(
        Swift_Mailer $mailer,
        Twig_Environment $twig,
        LoggerInterface $logger,
        array $templates
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->templates = $templates;
    }

    /**
     * @TODO thow exception when a block is not found (maybe)
     */
    public function sendTo(MailableInterface $recipient, array $payload = []): int
    {
        if (!$this->isEnabled) {
            $this->logger->notice("The quickmailer {$this->name} is disabled.");

            return 0;
        }

        if (!$this->from) {
            $this->logger->error("From field is not set.");
            throw new \RuntimeException("Please set the `from` field in the QuickMailer config.");
        }


        $data = array_merge($this->defaultData, $payload);

        $subject = $this->getSubject($data);
        $htmlBody = $this->getHtmlBody($data);
        $textBody = $this->getTextBody($data);

        try {
            /** @var \Swift_Message $message */
            $message = $this->mailer->createMessage()
                ->setFrom([$this->from->getEmail() => $this->from->getName()])
                ->setTo([$recipient->getEmail() => $recipient->getName()])
                ->setSubject($subject)
                ->addPart($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain')
            ;
            if ($this->replyTo) {
                $message->setReplyTo([$this->replyTo->getEmail() => $this->replyTo->getName()]);
            }

            $this->logger->info("Sending email...", [
                'id' => $message->getId(),
                'name' => $this->name,
                'from' => $this->from ? [$this->from->getName(), $this->from->getEmail()] : null,
                'reply_to' => $this->replyTo ? [$this->replyTo->getName(), $this->replyTo->getEmail()] : null,
                'to' => $recipient ? [$recipient->getName(), $recipient->getEmail()] : null,
            ]);

            return $this->mailer->send($message);
        } catch (Throwable $exception) {
            $this->logger->critical("The email cannot be sent!", [
                'message' => $exception->getMessage(),
                'name' => $this->name,
                'from' => $this->from ? [$this->from->getName(), $this->from->getEmail()] : null,
                'reply_to' => $this->replyTo ? [$this->replyTo->getName(), $this->replyTo->getEmail()] : null,
                'to' => $recipient ? [$recipient->getName(), $recipient->getEmail()] : null,
            ]);
        }

        return 0;
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

    /**
     * @todo memoize template after creation
     * @throws TemplateNotFound
     */
    public function get(string $name): Template
    {
        if (!array_key_exists($name, $this->templates)) {
            throw new TemplateNotFound("Template “{$name}” not found.");
        }

        return new Template($this, $this->twig, $this->templates[$name]);
    }
}
