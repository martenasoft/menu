<?php

namespace MartenaSoft\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Common\Entity\CommonEntityInterface;
use MartenaSoft\Common\Entity\ConfirmDeleteEntity;
use MartenaSoft\Common\Entity\SafeDeleteEntityInterface;
use MartenaSoft\Common\Form\ConfirmDeleteFormType;
use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Entity\Config;
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


    public function delete(Request $request, Menu $menu): Response
    {
        if ($request->getMethod() != Request::METHOD_POST) {
            return $this->confirmDelete($request, $menu, 'menu_root_admin_index');
        } else {
            $post = $request->request->get('confirm_delete_form');
            $isSafeDelete = !empty($post['isSafeDelete']);

            try {
                $this->getEntityManager()->beginTransaction();
                if ($isSafeDelete) {
                    $menu->setIsDeleted(true);
                    $this->getEntityManager()->flush();
                } else {
                    $this->getMenuRepository()->delete($menu);
                }
                $this->getEntityManager()->commit();
                $this->addFlash(
                    CommonValues::FLASH_SUCCESS_TYPE,
                    CommonValues::FLUSH_SUCCESS_DELETE_MESSAGE
                );
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
                $this->getEntityManager()->rollback();
            }
        }

        return $this->redirectToRoute('menu_root_admin_index');
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
}
