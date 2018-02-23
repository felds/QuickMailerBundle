<?php

namespace Felds\QuickMailerBundle\DependencyInjection;

use Felds\QuickMailerBundle\EventListener\TransportException;
use Felds\QuickMailerBundle\Model\Mailable;
use Felds\QuickMailerBundle\QuickMailer;
use Psr\Log\NullLogger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony2-document.readthedocs.io/en/latest/book/service_container.html
 * @see http://symfony2-document.readthedocs.io/en/latest/cookbook/service_container/parentservices.html
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class QuickMailerExtension extends Extension
{
    public function getAlias(): string
    {
        return 'quickmailer';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // configure logger
        $logger = $this->setLoggerDefinition($container, $config['logger']);

        // create config services
        $from       = $this->setMailableDefinition($container, 'from', $config['from'] ?? null);
        $replyTo    = $this->setMailableDefinition($container, 'reply_to', $config['reply_to'] ?? null);

        foreach ($config['templates'] as $name => $args) {
            // create the new mailer id
            $id = 'quickmailer.' . $name;

            // setup the basic definition
            $definition = new Definition(QuickMailer::class, [
                new Reference('mailer'),
                new Reference('twig'),
                new Reference($logger),
                $args['template'],
                $name,
                $args['enabled'],
            ]);


            // add from and reply-to fields when needed
            if ($from) {
                $definition->addMethodCall('setFrom', [ new Reference($from) ]);
            }
            if ($replyTo) {
                $definition->addMethodCall('setReplyTo', [ new Reference($replyTo) ]);
            }

            // add the definition to the container
            $container->setDefinition($id, $definition);
        }

        $this->addLoggingListeners($container, $logger, $config['mailer']);
    }


    private function setMailableDefinition(ContainerBuilder $container, string $name, array $config = null)
    {
        // validate config
        if ($config === null) {
            return;
        }

        // create a new service id
        $id = 'quickmailer.config.' . $name;

        // add it to the container
        $definition = new Definition(Mailable::class, [
            $config['name'],
            $config['email'],
        ]);
        $container->setDefinition($id, $definition);

        return $id;
    }

    private function setLoggerDefinition(ContainerBuilder $container, $config)
    {
        $id = 'quickmailer.logger';

        if ($config) {
            $container->setAlias($id, $config);
        } else {
            $definition = new Definition(NullLogger::class);
            $container->setDefinition($id, $definition);
        }

        return $id;
    }

    private function addLoggingListeners(ContainerBuilder $container, string $logger, string $mailer)
    {
        $id = 'quickmailer.listener.transport_exception';

        $definition = new Definition(TransportException::class, [
            new Reference($logger),
        ]);
        $definition->addTag(sprintf('swiftmailer.%s.plugin', $mailer));

        $container->setDefinition($id, $definition);
    }
}
