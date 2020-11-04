<?php

namespace MartenaSoft\Menu\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Common\Repository\AbstractNestedSetServiceRepository;
use MartenaSoft\Menu\Entity\Menu;

class MenuRepository extends AbstractNestedSetServiceRepository
{
    protected string $alias = 'm';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function getAllQueryBuilder(): QueryBuilder
    {
        return $this
            ->createQueryBuilder($this->alias)
            ->orderBy("{$this->alias}.tree", "ASC")
            ->addOrderBy("{$this->alias}.lft", "ASC");
    }
}