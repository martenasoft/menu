<?php

namespace MartenaSoft\Menu\Controller;

use MartenaSoft\Menu\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractController
{
    private MenuRepository $menuRepository;

    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function vertical(string $name): Response
    {
        $menu = $this->menuRepository->findOneByName($name);
        $items = $this->menuRepository->getAllSubItemsQueryBuilder($menu)->getQuery()->getResult();
        return $this->render('@MartenaSoftMenu/menu/vertical.html.twig', [
            'menu' => $menu,
            'items' => $items
        ]);
    }

    public function horizontal(string $name): Response
    {
        $menu = $this->menuRepository->findOneByName($name);
        $items = $this->menuRepository
            ->getAllSubItemsQueryBuilder($menu)
            ->andWhere("m.lvl=2")
            ->getQuery()->getResult();
        return $this->render('@MartenaSoftMenu/menu/horizontal.html.twig', [
            'menu' => $menu,
            'items' => $items
        ]);
    }
}