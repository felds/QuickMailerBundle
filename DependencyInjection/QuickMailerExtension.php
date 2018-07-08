<?php

namespace Felds\QuickMailerBundle\DependencyInjection;

use Felds\QuickMailerBundle\EventListener\Send;
use Felds\QuickMailerBundle\EventListener\TransportException;
use Felds\QuickMailerBundle\Model\Mailable;
use Felds\QuickMailerBundle\QuickMailer;
use Felds\QuickMailerBundle\QuickMailerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 * @see http://symfony2-document.readthedocs.io/en/latest/book/service_container.html
 * @see http://symfony2-document.readthedocs.io/en/latest/cookbook/service_container/parentservices.html
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class QuickMailerExtension extends Extension
{
    public function getAlias(): string
    {
        return 'felds_quickmailer';
    }

    
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $id = $this->getAlias();


        // process the configuration file(s)
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);


        $loggerReference = $this->getLoggerReference($container, $config);
        $mailerReference = $this->getMailerReference($container, $config);



        $fromReference = $this->setMailableDefinition($container, 'from', $config);
        $replyToReference = $this->setMailableDefinition($container, 'reply_to', $config);

        $definition = new Definition(QuickMailer::class, [
            $mailerReference,
            new Reference('twig'), // @todo make it dynamic?
            $loggerReference,
            $config['templates'],
            $fromReference,
            $replyToReference,
        ]);
        $container->setDefinition($id, $definition);

        // make it injectable
        $alias = new Alias($id);
        $container->setAlias(QuickMailer::class, $alias);

        // log mailer events
        // $this->addLoggingListeners($container, $logger, $config['mailer']);
    }


    private function getLoggerReference(ContainerBuilder $container, $config): ?Reference
    {
        $id = 'felds_quickmailer.config.logger';

        if ($config['logger']) {
            $container->setAlias($id, $config['logger']);
        } else {
            $definition = new Definition(NullLogger::class);
            $container->setDefinition($id, $definition);
        }

        return new Reference($id);
    }


    private function setMailableDefinition(ContainerBuilder $container, string $name, array $config): Reference
    {
        // validate config
        if (!array_key_exists($name, $config)) {
            return null;
        }

        // create a new service id
        $id = 'felds_quickmailer.config.'.$name;

        // add it to the container
        $definition = new Definition(
            Mailable::class, [
                $config[$name]['name'],
                $config[$name]['email'],
            ]
        );
        $container->setDefinition($id, $definition);

        return new Reference($id);
    }


    private function addLoggingListeners(ContainerBuilder $container, string $logger, string $mailer): void
    {
        // transport exception
        $id = 'quickmailer.listener.transport_exception';

        $definition = new Definition(
            TransportException::class, [
                new Reference($logger),
            ]
        );
        $definition->addTag(sprintf('swiftmailer.%s.plugin', $mailer));

        $container->setDefinition($id, $definition);

        // send
        $id = 'quickmailer.listener.send';
        $definition = new Definition(
            Send::class, [
                new Reference($logger),
            ]
        );
        $definition->addTag(sprintf('swiftmailer.%s.plugin', $mailer));

        $container->setDefinition($id, $definition);
    }


    private function getMailerReference(ContainerBuilder $container, $configs): Reference
    {
        $id = "{$this->getAlias()}.config.mailer";
        $config = $configs['mailer'];

        if ($config !== false) {
            $container->setAlias($id, $config);
        } else {
            $transportId = "{$id}.null_transport";
            $transport = new Definition(\Swift_NullTransport::class);
            $container->setDefinition($transportId, $transport);

            $definition = new Definition(\Swift_Mailer::class, [
                new Reference($transportId),
            ]);
            $container->setDefinition($id, $definition);
        }

        return new Reference($id);
    }
}
