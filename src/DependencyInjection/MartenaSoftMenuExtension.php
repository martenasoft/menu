<?php

namespace MartenaSoft\Menu\DependencyInjection;

use MartenaSoft\Menu\MartenaSoftMenuBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MartenaSoftMenuExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(MartenaSoftMenuBundle::getConfigName(), $config);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__).'/Resources/config')
        );
        $loader->load('services.yaml');
    }
}