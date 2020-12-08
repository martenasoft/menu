<?php

namespace MartenaSoft\Menu\Service;

use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Common\Event\CommonFormBeforeSaveEvent;
use MartenaSoft\Menu\Entity\MenuInterface;
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
            $this->entityManager->beginTransaction();

            if (empty($menuEntity->getConfig()) || empty($menuConfig)) {
                $menuConfig = $this->menuRepository->getConfig($menuEntity);

                if (empty($menuConfig->getId())) {
                    $menuEntity->setConfig($menuConfig);
                    $menuConfig->setName($menuEntity->getName());
                    $this->entityManager->persist($menuConfig);
                    $this->entityManager->flush();
                }
            }

            if ($menuEntity->getId() === null) {
                $this->menuRepository->create($menuEntity, $parent);
            } else {
                $this->menuRepository->move($menuEntity, $parent);
                $this->reInitUrlPath($menuEntity, false);

            }

            switch ($menuConfig->getUrlPathType()) {
                default:
                    $menuEntity->setPath($this->menuUrlService->urlPathFromItem($menuEntity));
            }

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    public function reInitUrlPath(MenuInterface $menu, bool $isFlash = true): void
    {
        $items = $this
            ->menuRepository
            ->getAllQueryBuilder()
            ->andWhere(MenuRepository::getAlias().'.tree=:tree')
            ->setParameter('tree', $menu->getTree())
            ->getQuery()
            ->getResult()
        ;

        foreach ($items as $item) {
            $item->setPath($this->menuUrlService->urlPathFromItem($item));
        }

        if ($isFlash) {
            $this->entityManager->flush();
        }
    }

}
