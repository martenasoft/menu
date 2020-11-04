<?php

namespace MartenaSoft\Menu\Repository;

use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\NestedSets\Entity\NodeInterface;
use MartenaSoft\NestedSets\Repository\AbstractMoveUpDown;

class MenuUpDownRepository extends AbstractMoveUpDown
{
    protected string $alias = 'm';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function up(NodeInterface $node): void
    {
        $this->change($node);
    }

    public function down(NodeInterface $node): void
    {
        $this->change($node, false);
    }
}