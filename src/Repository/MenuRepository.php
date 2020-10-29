<?php

namespace MartenaSoft\Menu\Repository;

use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Common\Repository\AbstractNestedSetServiceRepository;
use MartenaSoft\Menu\Entity\Menu;

class MenuRepository extends AbstractNestedSetServiceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }
}