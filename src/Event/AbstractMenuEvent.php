<?php

namespace MartenaSoft\Menu\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Common\Event\CommonEventInterface;
use MartenaSoft\Menu\Entity\MenuInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractMenuEvent extends Event implements CommonEventInterface, SaveMenuEventInterface
{
    private MenuInterface $menu;
    private CommonEntityInterface $entity;

    public function __construct(MenuInterface $menu, CommonEntityInterface $entity)
    {
        $this->menu = $menu;
        $this->entity = $entity;
    }

    public function getMenu(): MenuInterface
    {
        return $this->menu;
    }

    public function getEntity(): CommonEntityInterface
    {
        return $this->entity;
    }


}
