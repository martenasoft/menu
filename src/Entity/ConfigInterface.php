<?php

namespace MartenaSoft\Menu\Entity;

use Doctrine\Common\Collections\Collection;

interface ConfigInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name): self;

    public function getType(): int;

    public function setType(int $type): self;

    public function getUrlPathType(): int;

    public function setUrlPathType(int $urlPathType): self;

    public function isDefault(): bool;

    public function setIsDefault(bool $isDefault): ?Config;

    public function addMenu(Menu $menu, bool $isSaveConfigInMenuEntity = false): self;

    public function getMenu(): ?Collection;

    public function setMenu(?Collection $menu): self;

    public function getDescriptions(): ?string;

    public function setDescriptions(?string $descriptions): self;
}
