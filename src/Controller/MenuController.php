<?php

namespace MartenaSoft\Menu\Controller;

use MartenaSoft\Common\Service\ConfigService\CommonConfigServiceInterface;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Entity\MenuInterface;
use MartenaSoft\Menu\MartenaSoftMenuBundle;
use MartenaSoft\Menu\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractController
{
    private MenuRepository $menuRepository;
    private array $config;

    public function __construct(MenuRepository $menuRepository, CommonConfigServiceInterface $configService)
    {
        $this->menuRepository = $menuRepository;
        $this->config = $configService->get(MartenaSoftMenuBundle::getConfigName());
    }

    public function vertical(Request $request, string $name = 'admin_vertical'): Response
    {
        $menu = $this->getMenuItem($this->config[$name]);
        $items = [];

        if ($menu) {
            $items = $this->menuRepository->getAllSubItemsQueryBuilder($menu)->getQuery()->getResult();
        }

        return $this->render('@MartenaSoftMenu/menu/vertical.html.twig', [
            'request' => $request,
            'menu' => $menu,
            'items' => $items
        ]);
    }

    public function horizontal(Request $request, string $name = 'admin_horizontal'): Response
    {
        $menu = $this->getMenuItem($this->config['admin_horizontal']);
        $items = [];

        if ($menu) {
            $items = $this->menuRepository
                ->getAllSubItemsQueryBuilder($menu)
                ->andWhere("m.lvl=2")
                ->getQuery()->getResult();
        }
        return $this->render('@MartenaSoftMenu/menu/horizontal.html.twig', [
            'request' => $request,
            'menu' => $menu,
            'items' => $items
        ]);
    }

    private function getMenuItem(string  $name): MenuInterface
    {
        $menu = $this->menuRepository->findOneByName($name);
        if (empty($menu)) {
            $menu = new Menu();
            $menu->setName($name);
            $this->menuRepository->create($menu);

        }
        return $menu;
    }
}