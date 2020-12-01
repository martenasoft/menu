<?php

namespace MartenaSoft\Menu;

use MartenaSoft\Common\CommonMartenaSoftBundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MartenaSoftMenuBundle extends Bundle implements CommonMartenaSoftBundleInterface
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public static function getConfigName(): string
    {
        return 'martena_soft_menu';
    }
}
