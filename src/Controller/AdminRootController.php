<?php

namespace MartenaSoft\Menu\Controller;

use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Form\RootManyType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminRootController extends AbstractMenuAdminController
{
    public function create(Request $request): Response
    {
        try {
            $menuEntity = new Menu();
            if (($form = $this->save($request, $menuEntity))->isSubmitted()) {
                $this->addFlash(CommonValues::FLASH_SUCCESS_TYPE, self::MENU_SAVED_SUCCESS_MESSAGE);
                return $this->redirectToRoute('menu_admin_config_save');
            }
        } catch (\Throwable $exception) {
            $this->getLogger()->error(
                CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE,
                [
                    'file' => __CLASS__,
                    'line' => $exception->getLine(),
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                ]
            );
            $this->addFlash(
                CommonValues::FLASH_ERROR_TYPE,
                CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE
            );
        }

        return $this->render(
            '@MartenaSoftMenu/admin_root/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    public function edit(Request $request, Menu $menuEntity): Response
    {
        try {
            if (($form = $this->save($request, $menuEntity))->isSubmitted()) {
                $this->addFlash(CommonValues::FLASH_SUCCESS_TYPE, self::MENU_SAVED_SUCCESS_MESSAGE);
                return $this->redirectToRoute('menu_admin_index');
            }
        } catch (\Throwable $exception) {
            $this->getLogger()->error(
                CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE,
                [
                    'file' => __CLASS__,
                    'line' => $exception->getLine(),
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                ]
            );
            $this->addFlash(
                CommonValues::FLASH_ERROR_TYPE,
                CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE
            );
        }

        return $this->render(
            '@MartenaSoftMenu/admin_root/edit.html.twig',
            [
                'form' => $form->createView(),
                'menuEntity' => $menuEntity
            ]
        );
    }

    private function save(Request $request, Menu $menu): FormInterface
    {
        $isShowConfigDropdown = ($this->getConfigRepository()->count([]) > 0);

        $form = $this->createForm(
            RootManyType::class,
            $menu,
            [
                'isShowConfigDropdown' => $isShowConfigDropdown
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEntityManager()->beginTransaction();
            try {
                if ($menu->getId() === null) {
                    $menu = $this->getMenuRepository()->create($menu);
                    $this->getEntityManager()->refresh($menu);
                }

                $this->getEntityManager()->flush();
                $this->getEntityManager()->commit();
            } catch (\Throwable $exception) {
                $this->getMenuRepository()->rollback();
                throw $exception;
            }
        }
        return $form;
    }

    protected function getReturnRouteName(): string
    {
        return 'menu_admin_index';
    }
}

