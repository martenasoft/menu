<?php

namespace MartenaSoft\Menu\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function getItemsQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder($this->alias);
        return $queryBuilder;
    }

    public function getOrCreateDefault(bool $isFlush = false): Config
    {
        $result = $this->findOneByisDefault(true);
        if (empty($result)) {
            $result = new Config();

            if ($isFlush) {
                $this->getEntityManager()->persist($result);
                $this->getEntityManager()->flush();
            }
        }
        return $result;
    }

    public function getAllQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder($this->alias);
    }

    public function setAllIsDefaultAsFalse(): int
    {
        $tableName = $this->getClassMetadata()->getTableName();
        $sql = "UPDATE `{$tableName}` SET `is_default` = 0";
        return $this->getEntityManager()->getConnection()->executeQuery($sql)->rowCount();
    }

    public function getCounts(): ?array
    {
        $sql = "SELECT count(*) AS total, count(IF(is_default = 1, id , NULL)) defaultCount FROM config;";
        return $this->getEntityManager()->getConnection()->fetchAssociative($sql);
    }

    public function getAlias(): string
    {
        return $this->alias;
    }
}
