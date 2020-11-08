<?php

namespace MartenaSoft\Menu\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use MartenaSoft\Menu\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ConfigRepository::class)
 * @UniqueEntity("name")
 */
class Config
{
    public const TYPE_OPEN = 1;
    public const TYPE_COLLAPSED = 2;
    public const TYPE_ACTIVE_OPEN = 3;
    public const URL_TYPE_PATH = 1;
    public const URL_TYPE_SINGLE = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(nullable=false, unique=true)
     */
    private ?string $name = '';

    /** @ORM\Column(type="smallint") */
    private int $type = self::TYPE_OPEN;

    /** @ORM\Column(type="smallint") */
    private int $urlPathType = self::URL_TYPE_PATH;

    /** @ORM\Column(type="boolean") */
    private bool $isDefault = true;

    /**
     * @ORM\OneToMany(targetEntity="MartenaSoft\Menu\Entity\Menu", mappedBy="config", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Collection $menu;

    /** @ORM\Column(type="text", nullable=true) */
    private ?string $descriptions;

    public function __construct()
    {
        $this->menu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getUrlPathType(): int
    {
        return $this->urlPathType;
    }

    public function setUrlPathType(int $urlPathType): self
    {
        $this->urlPathType = $urlPathType;
        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): ?Config
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    public function addMenu(Menu $menu, bool $isSaveConfigInMenuEntity = false): self
    {
        if (!$this->menu->contains($menu)) {
            $this->menu[] = $menu;

            if ($isSaveConfigInMenuEntity) {
                $menu->setConfig($this);
            }
        }
        return $this;
    }

    public function getMenu(): ?Collection
    {
        return $this->menu;
    }

    public function setMenu(?Collection $menu): self
    {
        $this->menu = $menu;
        return $this;
    }

    public function getDescriptions(): ?string
    {
        return $this->descriptions;
    }

    public function setDescriptions(?string $descriptions): self
    {
        $this->descriptions = $descriptions;
        return $this;
    }
}
