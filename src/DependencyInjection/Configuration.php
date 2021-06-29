<?php

namespace SymfonySimpleSite\Menu\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use SymfonySimpleSite\Menu\MenuBundle;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(MenuBundle::getConfigName());

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('admin_vertical')->defaultValue('Admin Vertical')->end()
            ->scalarNode('admin_horizontal')->defaultValue('Admin Horizontal')->end()
            ->scalarNode('content_horizontal')->defaultValue('Content Horizontal')->end()
            ->scalarNode('content_vertical')->defaultValue('Content Vertical')->end()
        ;

        return $treeBuilder;
    }
}
