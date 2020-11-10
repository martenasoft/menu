<?php

namespace MartenaSoft\Menu\Entity;

use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Common\Entity\SafeDeleteEntityInterface;
use MartenaSoft\Menu\Repository\MenuRepository;
use Doctrine\ORM\Mapping as ORM;
use MartenaSoft\NestedSets\Entity\NodeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MenuRepository::class)
 * @UniqueEntity(
 *     fields={"name", "tree"},
 *     errorPath="name"
 * )
 *
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(
 *                  name="lft", columns={"lft"},
 *                  name="lft_rgt", columns={"lft", "rgt"},
 *                  name="id_lft_rgt", columns={"id", "lft", "rgt"}
 *              )
 *          }
 *     )
 */
class Menu implements CommonEntityInterface, NodeInterface, SafeDeleteEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=65)
     */
    private ?string $name = null;

    /** @ORM\Column(type="integer") */
    private ?int $lft;

    /** @ORM\Column(type="integer") */
    private ?int $rgt;

    /** @ORM\Column(type="integer") */
    private ?int $lvl;

    /** @ORM\Column(type="integer") */
    private ?int $tree = null;

    /** @ORM\Column(type="integer") */
    private ?int $parentId = null;

    /**
     * @ORM\ManyToOne(targetEntity="MartenaSoft\Menu\Entity\Config", inversedBy="menu", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Config $config;

    /** @ORM\Column(type="boolean")   */
    private bool $isDeleted = false;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $route;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $url;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
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

    public function getConfig(): ?Config
    {
        return $this->config;
    }

    public function setConfig(?Config $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(?string $route): self
    {
        $this->route = $route;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }


}
