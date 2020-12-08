<?php

namespace MartenaSoft\Menu\Service;

use MartenaSoft\Menu\Entity\MenuInterface;

interface SaveMenuItemServiceInterface
{
    public function getMenuByName(string $name): ?MenuInterface;
    public function save(MenuInterface $menuEntity, ?MenuInterface $parent, ?Config $menuConfig = null): void;
    public function reInitUrlPath(MenuInterface $menu, bool $isFlash = true): void;
}