<?php

namespace MartenaSoft\Menu\Entity;

use MartenaSoft\Common\Entity\NestedSetEntityInterface;
use MartenaSoft\Menu\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 */
class Menu implements NestedSetEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /** @ORM\Column(type="string", length=65) */
    private string $name;

    /** @ORM\Column(type="integer") */
    private int $lft;

    /** @ORM\Column(type="integer") */
    private int $rgt;

    /** @ORM\Column(type="integer") */
    private int $lvl;

    /** @ORM\Column(type="integer") */
    private int $tree;

    /** @ORM\Column(type="integer") */
    private int $parentId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): ?self
    {
        $this->name = $name;
        return $this;
    }

    public function getLft(): ?int
    {
        return $this->lft;
    }

    public function setLft(?int $lft): self
    {
        $this->lft = $lft;
        return $this;
    }

    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    public function setRgt(?int $rgt): self
    {
        $this->rgt = $rgt;
        return $this;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }
   
    public function setLvl(?int $lvl): self
    {
        $this->lvl = $lvl;
        return $this;
    }
    
    public function getTree(): ?int
    {
        return $this->tree;
    }

    public function setTree(?int $tree): self
    {
        $this->tree = $tree;
        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;
        return $this;
    }
}