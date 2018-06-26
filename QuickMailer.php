<?php

namespace Felds\QuickMailerBundle;

use Felds\QuickMailerBundle\Exception\TemplateNotFound;
use Felds\QuickMailerBundle\Model\Mailable;
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
     * @var MailableInterface
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
        array $templates,
        Mailable $from,
        ?Mailable $replyTo = null
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->from = $from;
        $this->replyTo = $replyTo;
        $this->templates = $templates;
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

        return new Template(
            $name,
            $this->templates[$name],
            $this->twig,
            $this->mailer,
            $this->logger,
            $this->from,
            $this->replyTo
        );
    }
}
