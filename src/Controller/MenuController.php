<?php

namespace SymfonySimpleSite\Menu\Controller;

use Symfony\Component\HttpFoundation\Response;
use SymfonySimpleSite\Menu\Entity\Menu;
use SymfonySimpleSite\Menu\Repository\MenuRepository;
use SymfonySimpleSite\Page\Controller\AbstractPageController;

class MenuController extends AbstractPageController
{
    public function topMenu(int $rootId, MenuRepository $menuRepository): Response
    {
        $menu = $this->getEntityManager()->find(Menu::class, $rootId);
        $items = $menuRepository
            ->getAllSubItemsQueryBuilder($menu)
            ->andWhere($menuRepository->getAlias().".lvl=:lvl")
            ->setParameter('lvl', $menu->getLvl() + 1)
            ->getQuery()
            ->getResult()
        ;
        return $this->render('@Menu/frontend/top_menu.html.twig',
            ['items' => $items]
        );
    }
}