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
        $rootNode = $treeBuilder->root('felds_quick_mailer');

        $rootNode
            ->children()
                ->arrayNode('defaults')
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
                        ->arrayNode('data')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')
                                ->isRequired()
                            ->end()
                        ->end() // data
                    ->end()
                ->end() // defaults
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
