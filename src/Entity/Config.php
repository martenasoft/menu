<?php

namespace MartenaSoft\Menu\Entity;

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
    private string $name = 'default';

    /** @ORM\Column(type="smallint") */
    private int $type = self::TYPE_OPEN;

    /** @ORM\Column(type="smallint") */
    private int $urlPathType = self::URL_TYPE_PATH;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
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
}
