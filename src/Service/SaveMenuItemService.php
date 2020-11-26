<?php

namespace MartenaSoft\Menu\Service;

use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Menu\Entity\MenuInterface;
use MartenaSoft\Menu\Repository\MenuRepository;

class SaveMenuItemService implements SaveMenuItemServiceInterface
{
    private MenuRepository $menuRepository;
    private EntityManagerInterface $entityManager;
    
    public function __construct(MenuRepository $menuRepository, EntityManagerInterface $entityManager)
    {
        $this->menuRepository = $menuRepository;
        $this->entityManager = $entityManager;
    }

    public function getMenuByName(string $name): ?MenuInterface
    {
        return $this
            ->menuRepository
            ->findOneByNameQueryBuilder($name)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    
    public function save(MenuInterface $menuEntity, ?MenuInterface $parent): void 
    {
        try {
            $this->entityManager->beginTransaction();
            if ($menuEntity->getId() === null) {
                $this->menuRepository->create($menuEntity, $parent);
            } else {

                if ($menuEntity->getParentId() != $parent->getId()) {

                    $parent = null;
                    if ($menuEntity->getParentId() > 0) {
                        $parent = $this->menuRepository->find($menuEntity->getParentId());
                    } else {
                        $defaultConfig = $this->getConfigRepository()->getOrCreateDefault();
                        $menuEntity->setConfig($defaultConfig);
                    }
                    $this->menuRepository->move($menuEntity, $parent);
                }
            }
            $this->entityManager->flush($menuEntity);
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
