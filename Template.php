<?php
declare(strict_types=1);

namespace Felds\QuickMailerBundle;

use Felds\QuickMailerBundle\Model\MailableInterface;
use Twig_Environment;
use Twig_TemplateWrapper;

class Template
{
    /**
     * @var QuickMailerInterface
     */
    private $quickMailer;

    /**
     * @var array<string, mixed>
     */
    private $config;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var Twig_TemplateWrapper
     */
    private $twigTemplate;

    public function __construct(QuickMailerInterface $quickMailer, Twig_Environment $twig, array $config)
    {
        $this->quickMailer = $quickMailer;
        $this->config = $config;
        $this->isEnabled = $config['enabled'];
        $this->twigTemplate = $twig->load($config['path']);
    }

    public function sendTo(MailableInterface $recipient, array $payload = []): int
    {
        return -1;
    }
}