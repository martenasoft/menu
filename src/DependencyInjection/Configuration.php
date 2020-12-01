<?php

namespace MartenaSoft\Menu\DependencyInjection;

use MartenaSoft\Menu\MartenaSoftMenuBundle;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(MartenaSoftMenuBundle::getConfigName());

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('admin_vertical')->defaultValue('Admin Vertical')->end()
            ->scalarNode('admin_horizontal')->defaultValue('Admin Horizontal')->end()
        ;

        return $treeBuilder;
    }
}
