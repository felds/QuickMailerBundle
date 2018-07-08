<?php

namespace Felds\QuickMailerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @link http://symfony.com/doc/current/cookbook/bundles/configuration.html
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('felds_quickmailer');

        $rootNode
            ->children()
                ->arrayNode('from')
                    ->isRequired()
                    ->children()
                        ->scalarNode('name')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->example("Sender Name")
                        ->end() // name
                        ->scalarNode('email')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->example("from@example.com")
                        ->end() // email
                    ->end()
                ->end() // from
                ->arrayNode('reply_to')
                    ->children()
                        ->scalarNode('name')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->example("Reply-To Name")
                        ->end() // name
                        ->scalarNode('email')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->example("reply-to@example.com")
                        ->end() // email
                    ->end()
                ->end() // reply_to
                ->arrayNode('templates')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->beforeNormalization()
                            ->ifString()
                                ->then(function ($template) { return ['path' => $template]; })
                        ->end()
                        ->children()
                            ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                            ->booleanNode('enabled')->defaultTrue()->end()
                        ->end()
                    ->end()
                ->end() // templates


                // Services

                ->scalarNode('logger')
                    ->example('logger')
                    ->defaultNull()
                ->end() // logger
                ->scalarNode('mailer_service')
                    ->example('mailer')
                    ->defaultValue('mailer')
                ->end() // mailer_service
            ->end()
        ;

        return $treeBuilder;
    }
}
