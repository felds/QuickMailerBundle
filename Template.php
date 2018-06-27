<?php
declare(strict_types=1);

namespace Felds\QuickMailerBundle;

use Felds\QuickMailerBundle\Model\MailableInterface;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Throwable;
use Twig_Environment;
use Twig_TemplateWrapper;

class Template
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array<string, array<string, mixed>> config
     */
    private $config;

    /**
     * @var MailableInterface
     */
    private $from;

    /**
     * @var MailableInterface|null
     */
    private $replyTo;

    /**
     * @var Twig_TemplateWrapper
     */
    private $templateWrapper;

    public function __construct(
        string $name,
        array $config,
        Twig_Environment $twig,
        Swift_Mailer $mailer,
        LoggerInterface $logger,
        MailableInterface $from,
        ?MailableInterface $replyTo = null
    ) {
        $this->name = $name;
        $this->config = $config;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->from = $from;
        $this->replyTo = $replyTo;
        $this->templateWrapper = $twig->load($config['path']);
    }

    /**
     * @param MailableInterface $recipient Who should receive the email
     * @param array<string, mixed> $payload Data to be used by the template.
     * @return int Number of emails sent (usually 1 on success; 0 on failure)
     */
    public function sendTo(MailableInterface $recipient, array $payload = []): int
    {
        if (!$this->config['enabled']) {
            $this->logger->notice("The quickmailer {$this->name} is disabled.");

            return 0;
        }

        $subject = $this->getSubject($payload);
        $htmlBody = $this->getPart('html', $payload);
        $textBody = $this->getPart('text', $payload);

        if (!$subject) {
            throw new \RuntimeException("A subject is required. Please add a non-empty `subject` block in the template {$this->config['path']}.");
        }

        if (!($htmlBody || $textBody)) {
            throw new \RuntimeException("The template {$this->config['path']} should have either an `html` block or a `text` block. Preferably both.");
        }

        try {
            /** @var \Swift_Message $message */
            $message = $this->mailer->createMessage()
                ->setFrom([$this->from->getEmail() => $this->from->getName()])
                ->setTo([$recipient->getEmail() => $recipient->getName()])
                ->setSubject($subject)
            ;

            if ($this->replyTo) {
                $message->setReplyTo([$this->replyTo->getEmail() => $this->replyTo->getName()]);
            }

            if ($htmlBody) {
                $message->addPart($htmlBody, 'text/html');
            }

            if ($textBody) {
                $message->addPart($textBody, 'text/plain');
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

    private function getSubject(array $payload): string
    {
        return trim((string)$this->getPart('subject', $payload));
    }

    private function getPart(string $blockName, $payload): ?string
    {
        if (in_array($blockName, $this->templateWrapper->getBlockNames())) {
            return $this->templateWrapper->renderBlock($blockName, $payload);
        } else {
            return null;
        }
    }
}