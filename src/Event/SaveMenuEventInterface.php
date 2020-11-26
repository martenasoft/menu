<?php

namespace MartenaSoft\Menu\Event;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Common\Event\CommonEventInterface;
use MartenaSoft\Menu\Entity\MenuInterface;

interface SaveMenuEventInterface extends CommonEventInterface
{
    public function getMenu(): MenuInterface;

    public function getEntity(): CommonEntityInterface;
}