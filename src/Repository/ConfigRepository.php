<?php

namespace MartenaSoft\Menu\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Menu\Entity\Config;

class ConfigRepository extends ServiceEntityRepository
{
    protected string $alias = 'mc';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    public function get(?string $name = null): Config
    {
        if (!empty($name)) {
            $entity = $this->findOneByName($name);
        }

        if (empty($entity)) {
            $entity = new Config();
            $this->getEntityManager()->persist($entity);
        }
        return $entity;
    }
}
