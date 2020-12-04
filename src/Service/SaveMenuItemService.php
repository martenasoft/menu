<?php

namespace MartenaSoft\Menu\Service;

use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Common\Event\CommonEventInterface;
use MartenaSoft\Common\Event\CommonFormBeforeSaveEvent;
use MartenaSoft\Common\Event\CommonFormEventEntityInterface;
use MartenaSoft\Menu\Entity\BaseMenuInterface;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Entity\MenuInterface;
use MartenaSoft\Menu\Exception\MenuMoveUnderOwnParentException;
use MartenaSoft\Menu\Repository\MenuRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormEvent;

class SaveMenuItemService implements SaveMenuItemServiceInterface
{
    private MenuRepository $menuRepository;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        MenuRepository $menuRepository,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->menuRepository = $menuRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getMenuByName(string $name): ?MenuInterface
    {
        return $this
            ->menuRepository
            ->findOneByNameQueryBuilder($name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function selectParentItemDropDown(FormEvent $event): void
    {
        $formData = $event->getData();
        $menu = $formData->getMenu();
        if (empty($menu) ||
            empty((int)$id = $menu->getId()) ||
            empty($parentMenuItem = $this->menuRepository->getParentByItemId($id))
        ) {
            return;
        }

        $formData->setMenu($parentMenuItem);
        $event->setData($formData);
    }

    public function initSaveMenuListener(string $menuFieldName, string $eventName): void
    {
        $this->eventDispatcher
            ->addListener(
                $eventName, function (CommonEventInterface $event)
                use ($menuFieldName) {

                $formData = $event->getForm()->getData();
                $menuData = $formData->getMenu();
                $menu = $this->getMenuEntity($formData, $menuFieldName);

                if (!empty($menu) && empty($menuData)) {
                    throw new ParentMenuIsEmptyException();
                }

                if ($menuData === null) {
                    return;
                }

                if ($menu === null || $formData->getId() === null) {
                    $menu = new Menu();
                    $menu->setName($formData->getName());
                }

                $parentMenu = null;

                if ($menu->getId() !== null) {
                    $this->menuRepository->getParentByItemId($menu->getId());
                }

                if (!empty($parentMenu) &&
                    !empty($menuData) &&
                    $parentMenu->getId() == $menuData->getId() &&
                    $parentMenu->getTree() == $menuData->getTree()) {
                    return;
                }

                try {
                    $this->save($menu, $menuData);
                    $event->getForm()->getData()->setMenu($menu);
                } catch (\Throwable $exception) {
                    throw $exception;
                }
            });
    }

    public function save(MenuInterface $menuEntity, ?MenuInterface $parent): void
    {
        try {
            $this->entityManager->beginTransaction();
            if ($menuEntity->getId() === null) {
                $this->menuRepository->create($menuEntity, $parent);
            } else {

                if ($menuEntity->getParentId() == $parent->getId() && $menuEntity->getTree() == $parent->getTree()) {
                    throw new MenuMoveUnderOwnParentException();
                }
                $this->menuRepository->move($menuEntity, $parent);
            }

            $this->entityManager->flush($menuEntity);
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    private function getMenuEntity(BaseMenuInterface $menuEntity, string $menuFieldName): ?MenuInterface
    {
        $menuId = $this->getMenuId($menuEntity, $menuFieldName);
        if (empty($menuId)) {
            return null;
        }

        return $this->entityManager->getRepository(Menu::class)->find($menuId);
    }

    private function getMenuId(BaseMenuInterface $menuEntity, string $menuFieldName): ?int
    {
        $tableName = $this->entityManager->getClassMetadata(get_class($menuEntity))->getTableName();
        return $this->entityManager->getConnection()->fetchOne(
            "SELECT $menuFieldName FROM $tableName WHERE id=:id",
            ["id" => $menuEntity->getId()]
        );
    }
}
