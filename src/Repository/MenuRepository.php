<?php

namespace MartenaSoft\Menu\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use MartenaSoft\Common\Repository\AbstractCommonRepository;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Entity\MenuInterface;
use MartenaSoft\NestedSets\Entity\NodeInterface;
use MartenaSoft\NestedSets\Repository\NestedSetsCreateDeleteInterface;
use MartenaSoft\NestedSets\Repository\NestedSetsMoveItemsInterface;
use MartenaSoft\NestedSets\Repository\NestedSetsMoveUpDownInterface;

class MenuRepository extends AbstractCommonRepository
    implements NestedSetsMoveItemsInterface,
                NestedSetsMoveUpDownInterface,
                NestedSetsCreateDeleteInterface
{
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

    public static function getAlias(): string
    {
        return 'm';
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
        $queryBuilder = $this
            ->getQueryBuilder()
            ->orderBy(static::getAlias().".tree", "ASC")
            ->addOrderBy(static::getAlias().".lft", "ASC");

        return $queryBuilder;
    }

    public function getAllSubItemsQueryBuilder(MenuInterface $menu):QueryBuilder
    {
        return $this
            ->getQueryBuilder()
            ->andWhere(static::getAlias().".tree=:tree")->setParameter("tree", $menu->getTree())
            ->andWhere(static::getAlias().".lft>:lft")->setParameter("lft", $menu->getLft())
            ->andWhere(static::getAlias().".rgt<:rgt")->setParameter("rgt", $menu->getRgt())
            ;
    }

    public function getAllRootsQueryBuilder(): QueryBuilder
    {
        return $this
            ->getQueryBuilder()
            ->andWhere(static::getAlias().".lft=:lft")
            ->setParameter("lft", 1);
    }

    public function move(NodeInterface $node, ?NodeInterface $parent): void
    {
        try {
            $this->nestedSetsMoveItems->move($node, $parent);
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public function upDown(NodeInterface $node, bool $isUp = true): void
    {
        $this->nestedSetsMoveUpDown->upDown($node, $isUp);
    }

    public function findOneByNameQueryBuilder(string $name): QueryBuilder
    {
        return $this
            ->getQueryBuilder()
            ->innerJoin("static::getAlias().config", "config")
            ->andWhere("static::getAlias().name=:name")
            ->setParameter("name", $name)
            ;
    }

    public function getParentByItemId(int $id): ?MenuInterface
    {
        $sql = "SELECT parent_id FROM menu WHERE `id`=:id";
        $parentId = $this->getEntityManager()->getConnection()->fetchOne($sql, ["id" => $id]);
        return $this->find($parentId);
    }

    public function getParentsByItemQueryBuilder(MenuInterface $menu): ?QueryBuilder
    {
        $alias = static::getAlias();

        return $this
            ->getQueryBuilder()
            ->andWhere("{$alias}.lft<:lft")->setParameter('lft', $menu->getLft())
            ->andWhere("{$alias}.rgt>:rgt")->setParameter('rgt', $menu->getRgt())
            ->andWhere("{$alias}.tree=:tree")->setParameter('tree', $menu->getTree())
        ;
    }
}
