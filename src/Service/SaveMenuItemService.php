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
use MartenaSoft\Menu\Repository\ConfigRepository;
use MartenaSoft\Menu\Repository\MenuRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormEvent;

class SaveMenuItemService implements SaveMenuItemServiceInterface
{
    private MenuRepository $menuRepository;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private MenuUrlService $menuUrlService;

    public function __construct(
        MenuRepository $menuRepository,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        MenuUrlService $menuUrlService
    ) {
        $this->menuRepository = $menuRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->menuUrlService = $menuUrlService;
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


    public function save(MenuInterface $menuEntity, ?MenuInterface $parent, ?Config $menuConfig = null): void
    {
        try {
            if (empty($menuEntity->getConfig()) || empty($menuConfig)) {
                $menuConfig = $this->menuRepository->getConfig($menuEntity);
                if (empty($menuConfig->getId())) {
                    $menuEntity->setConfig($menuConfig);
                    $menuConfig->setName($menuEntity->getName());
                    $this->entityManager->persist($menuConfig);
                    $this->entityManager->flush();
                }
            }

            $this->entityManager->beginTransaction();
            if ($menuEntity->getId() === null) {
                $this->menuRepository->create($menuEntity, $parent);
            } elseif ($menuEntity->getParentId() != $parent->getId() || $menuEntity->getTree() != $parent->getTree()) {

                $this->menuRepository->move($menuEntity, $parent);
            }


            switch ($menuConfig->getUrlPathType()) {
                default:
                    $menuEntity->setPath($this->menuUrlService->urlPathFromItem($menuEntity));
            }

            $this->entityManager->flush($menuEntity);
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
