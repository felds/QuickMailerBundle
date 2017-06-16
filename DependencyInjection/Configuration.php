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
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end() // templates
            ->end()
        ;

        return $treeBuilder;
    }
}
