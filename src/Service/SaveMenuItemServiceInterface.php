<?php

namespace MartenaSoft\Menu\Service;

use MartenaSoft\Menu\Entity\MenuInterface;

interface SaveMenuItemServiceInterface
{
    public function getMenuByName(string $name): ?MenuInterface;
    public function save(MenuInterface $menuEntity, ?MenuInterface $parent): void;
}