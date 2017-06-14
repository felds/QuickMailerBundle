<?php

namespace Felds\QuickMailerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Felds\QuickMailerBundle\QuickMailer;
use Felds\QuickMailerBundle\Model\Mailable;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony2-document.readthedocs.io/en/latest/book/service_container.html
 * @see http://symfony2-document.readthedocs.io/en/latest/cookbook/service_container/parentservices.html
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class FeldsQuickMailerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $fromName   = $config['defaults']['from_name'];
        $fromEmail  = $config['defaults']['from_email'];

        foreach ($config['templates'] as $name => $template) {
            $id = 'quickmailer.' . $name;

            $definition = new Definition(QuickMailer::class, [
                new Reference('mailer'),
                new Reference('twig'),
                $template,
            ]);
            $definition->addMethodCall('setFromByNameAndEmail', [ $fromName, $fromEmail ]);

            $container->setDefinition($id, $definition);
        }
    }
}
