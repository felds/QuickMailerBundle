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
        $rootNode = $treeBuilder->root('quickmailer');

        $rootNode
            ->children()
                ->arrayNode('from')
                    ->isRequired()
                    ->children()
                        ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('email')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end() // from
                ->arrayNode('reply_to')
                    ->children()
                        ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('email')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end() // reply_to
                ->arrayNode('templates')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->beforeNormalization()
                            ->ifString()
                                ->then(function ($template) { return ['template' => $template]; })
                        ->end()
                        ->children()
                            ->scalarNode('template')->isRequired()->cannotBeEmpty()->end()
                            ->booleanNode('enabled')->defaultTrue()->end()
                        ->end()
                    ->end()
                ->end() // templates
                ->scalarNode('logger_service')->defaultValue('logger')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
