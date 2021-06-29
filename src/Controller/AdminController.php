<?php

namespace SymfonySimpleSite\Menu\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SymfonySimpleSite\Common\Interfaces\StatusInterface;
use SymfonySimpleSite\Menu\Entity\Menu;
use SymfonySimpleSite\Menu\Form\MenuType;
use SymfonySimpleSite\Menu\Repository\MenuRepository;
use SymfonySimpleSite\Page\Controller\AbstractAdminController;

class AdminController extends AbstractAdminController
{
    public function index(): Response
    {
        return $this->render('@Menu/admin/index.html.twig', [
            'template' => $this->getTemplate()
        ]);
    }

    public function newRoot(Request $request, MenuRepository $menuRepository): Response
    {
        $menu = new Menu();
        $menu->setStatus(StatusInterface::STATUS_ACTIVE);
        $menu->setCreatedAt(new \DateTime('now'));

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $menuRepository->create($menu);
                return $this->redirectToRoute('menu_admin_index');
            } catch (\Throwable $exception) {
                throw $exception;
               $this->addFlash("Error", "Error");
            }
        }

        return $this->render('@Menu/admin/edit.html.twig', [
            'template' => $this->getTemplate(),
            'form' => $form->createView()
        ]);
    }


    public function newSubMenu(Request $request, Menu $parent, MenuRepository $menuRepository): Response
    {
        $menu = new Menu();
        $menu->setStatus(StatusInterface::STATUS_ACTIVE);
        $menu->setCreatedAt(new \DateTime('now'));

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $menuRepository->create($menu, $parent);
                return $this->redirectToRoute('menu_admin_index');
            } catch (\Throwable $exception) {
                throw $exception;
                $this->addFlash("Error", "Error");
            }
        }

        return $this->render('@Menu/admin/edit.html.twig', [
            'template' => $this->getTemplate(),
            'form' => $form->createView(),
            'menuItem' => $parent
        ]);
    }

    public function edit(Request $request, Menu $menu, MenuRepository $menuRepository): Response
    {
        $menu->setStatus(StatusInterface::STATUS_ACTIVE);
        $menu->setUpdatedAt(new \DateTime('now'));

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $menuRepository->create($menu);
                return $this->redirectToRoute('menu_admin_index');
            } catch (\Throwable $exception) {
                throw $exception;
                $this->addFlash("Error", "Error");
            }
        }

        return $this->render('@Menu/admin/edit.html.twig', [
            'template' => $this->getTemplate(),
            'form' => $form->createView(),
            'menuItem' => $menu
        ]);
    }

    public function treeMenu(MenuRepository $menuRepository): Response
    {
        return $this->render('@Menu/admin/tree_menu.html.twig', [
            'items' => $menuRepository
                ->getAllQueryBuilder()
                ->getQuery()
                ->getResult()
        ]);
    }
}
