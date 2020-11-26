<?php

namespace MartenaSoft\Menu\Entity;

interface BaseMenuInterface
{
    public function getMenu(): ?Menu;

    public function setMenu(?Menu $menu): self;
}
