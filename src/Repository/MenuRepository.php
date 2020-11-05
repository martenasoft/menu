<?php

namespace MartenaSoft\Menu\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Common\Entity\NestedSetEntityInterface;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\NestedSets\Entity\NodeInterface;
use MartenaSoft\NestedSets\Repository\NestedSetsMoveItemsInterface;
use MartenaSoft\NestedSets\Repository\NestedSetsMoveUpDown;
use MartenaSoft\NestedSets\Repository\NestedSetsMoveUpDownInterface;

class MenuRepository extends ServiceEntityRepository
    implements NestedSetsMoveItemsInterface, NestedSetsMoveUpDownInterface
{
    protected string $alias = 'm';
    private NestedSetsMoveNode $nestedSetsMoveNode;

    public function __construct(
        ManagerRegistry $registry,
        NestedSetsMoveItemsInterface $nestedSetsMoveNode,
        NestedSetsMoveUpDownInterface $moveUpDown
    ) {
        parent::__construct($registry, Menu::class);
        $this->nestedSetsMoveNode = $nestedSetsMoveNode;
        $this->moveUpDown = $moveUpDown;
    }

    public function getAllQueryBuilder(): QueryBuilder
    {
        return $this
            ->createQueryBuilder($this->alias)
            ->orderBy("{$this->alias}.tree", "ASC")
            ->addOrderBy("{$this->alias}.lft", "ASC");
    }

    public function move(NestedSetEntityInterface $node, ?NestedSetEntityInterface $parent): void
    {
        $this->nestedSetsMoveNode->move($node, $parent);
    }

    public function change(NodeInterface $node, bool $isUp = true): void
    {
        $this->moveUpDown->change($node, $isUp);
    }
}