<?php

namespace SymfonySimpleSite\Menu;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use SymfonySimpleSite\Common\Interfaces\BundleInterface;

class MenuBundle extends Bundle implements BundleInterface
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public static function getConfigName(): string
    {
        return 'menu';
    }
}
