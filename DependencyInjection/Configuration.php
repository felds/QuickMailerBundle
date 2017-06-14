<?php

namespace Felds\QuickMailerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
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

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
