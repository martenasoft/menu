<?php

namespace MartenaSoft\Menu\Service;

use MartenaSoft\Common\Event\CommonEventInterface;
use MartenaSoft\Menu\Entity\MenuInterface;

interface SaveMenuItemServiceInterface
{
    public function getMenuByName(string $name): ?MenuInterface;
    public function save(MenuInterface $menuEntity, ?MenuInterface $parent, ?Config $menuConfig = null): void;
}