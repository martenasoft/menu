<?php

namespace MartenaSoft\Menu\Entity;

interface MenuInterface
{
    public const URL_TYPE_TRANSLITERATED = 1;
    public const URL_TYPE_SHORT = 2;

    public function getId(): ?int;

    public function setId(?int $id): self;

    public function getName(): ?string;

    public function setName(?string $name): self;

    public function getLft(): ?int;

    public function setLft(?int $lft): self;

    public function getRgt(): ?int;

    public function setRgt(?int $rgt): self;

    public function getLvl(): ?int;

    public function setLvl(?int $lvl): self;

    public function getTree(): ?int;

    public function setTree(?int $tree): self;

    public function getParentId(): ?int;

    public function setParentId(?int $parentId): self;

    public function getConfig(): ?Config;

    public function setConfig(?Config $config): self;

    public function isDeleted(): ?bool;

    public function setIsDeleted(?bool $isDeleted): self;

    public function getRoute(): ?string;

    public function setRoute(?string $route): self;

    public function getUrl(): ?string;

    public function setUrl(?string $url): self;

    public function getPath(): ?string;

    public function setPath(?string $path): self;

    public function getTransliteratedUrl(int $type = self::URL_TYPE_TRANSLITERATED): string;
}