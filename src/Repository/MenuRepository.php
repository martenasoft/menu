<?php

namespace MartenaSoft\Menu\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\NestedSets\Entity\NodeInterface;
use MartenaSoft\NestedSets\Repository\NestedSetsCreateDeleteInterface;
use MartenaSoft\NestedSets\Repository\NestedSetsMoveItemsInterface;
use MartenaSoft\NestedSets\Repository\NestedSetsMoveUpDownInterface;

class MenuRepository extends ServiceEntityRepository
    implements NestedSetsMoveItemsInterface,
                NestedSetsMoveUpDownInterface,
                NestedSetsCreateDeleteInterface
{
    protected string $alias = 'm';
    private NestedSetsMoveItemsInterface $nestedSetsMoveItems;
    private NestedSetsMoveUpDownInterface $nestedSetsMoveUpDown;
    private NestedSetsCreateDeleteInterface $nestedSetsCreateDelete;

    public function __construct(
        ManagerRegistry $registry,
        NestedSetsMoveItemsInterface $nestedSetsMoveItems,
        NestedSetsMoveUpDownInterface $nestedSetsMoveUpDown,
        NestedSetsCreateDeleteInterface $nestedSetsCreateDelete
    ) {
        parent::__construct($registry, Menu::class);

        $this->nestedSetsMoveItems = $nestedSetsMoveItems;
        $this->nestedSetsMoveUpDown = $nestedSetsMoveUpDown;
        $this->nestedSetsCreateDelete = $nestedSetsCreateDelete;

        $this->nestedSetsMoveUpDown ->setEntityClassName(Menu::class);
        $this->nestedSetsMoveItems->setEntityClassName(Menu::class);
        $this->nestedSetsCreateDelete->setEntityClassName(Menu::class);

    }

    public function create(NodeInterface $node, ?NodeInterface $parent = null): NodeInterface
    {
        return $this->nestedSetsCreateDelete->create($node, $parent);
    }

    public function delete(NodeInterface $node, bool $isSafeDelete = true): void
    {
        $this->nestedSetsCreateDelete->delete($node, $isSafeDelete);
    }

    public function getAllQueryBuilder(): QueryBuilder
    {
        return $this
            ->createQueryBuilder($this->alias)
            ->orderBy("{$this->alias}.tree", "ASC")
            ->addOrderBy("{$this->alias}.lft", "ASC");
    }

    public function getAllRootsQueryBuilder(): QueryBuilder
    {
        return $this
            ->createQueryBuilder($this->alias)
            ->andWhere("{$this->alias}.lft=:lft")
            ->setParameter("lft", 1);
    }

    public function move(NodeInterface $node, ?NodeInterface $parent): void
    {
        $this->nestedSetsMoveItems->move($node, $parent);
    }

    public function change(NodeInterface $node, bool $isUp = true): void
    {
        $this->nestedSetsMoveUpDown->change($node, $isUp);
    }
}