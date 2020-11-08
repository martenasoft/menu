<?php

namespace MartenaSoft\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MartenaSoft\Common\Exception\ElementNotFoundException;
use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Entity\Config;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Form\MenuType;
use MartenaSoft\Menu\Form\RootManyType;
use MartenaSoft\Menu\Repository\ConfigRepository;
use MartenaSoft\Menu\Repository\MenuRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractMenuAdminController
{
    public const MENU_MOVED_DOWN_SUCCESS_MESSAGE = 'Menu moved down';
    public const MENU_MOVED_UP_SUCCESS_MESSAGE = 'Menu moved up';

    public function index(PaginatorInterface $paginator, int $page = 1): Response
    {
        $itemQuery = $this->getMenuRepository()->getAllQueryBuilder()->getQuery();
        $pagination = $paginator->paginate(
            $itemQuery,
            $page,
            CommonValues::ADMIN_PAGINATION_LIMIT
        );
        return $this->render('@MartenaSoftMenu/admin/index.html.twig', ['pagination' => $pagination]);
    }

    public function create(Menu $parent): Response
    {

        
    //    if (($form = $this->save()))

        return $this->render(
            '@MartenaSoftMenu/admin/save.html.twig',
            [
                'form' => $form->createView()
            ]
        );
        $this->getLogger()->error(
            CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE,
            [
                'file' => __CLASS__,
                'func' => __FUNCTION__,
                'line' => __LINE__,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ]
        );
        $this->addFlash(
            CommonValues::FLASH_ERROR_TYPE,
            CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE
        );

        //            $this->addFlash(CommonValues::FLASH_SUCCESS_TYPE, self::MENU_SAVED_SUCCESS_MESSAGE);
        //
//return $this->redirectToRoute('menu_admin_index');
    }

    private function save(Request $request, Menu $menuEntity, ?Menu $parent = null): FormInterface
    {
        $form = $this->createForm(MenuType::class, $menuEntity, ['menu' => $menuEntity]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->getEntityManager()->beginTransaction();
                if ($menuEntity->getId() === null) {
                    $this->getMenuRepository()->create($menuEntity, $parent);
                }
                $this->getMenuRepository()->flush($menuEntity);
                $this->getEntityManager()->commit();
                return $form;
            } catch (\Throwable $exception) {
                $this->getEntityManager()->rollback();
                throw $exception;
            }
        }
    }

    public function down(int $id): Response
    {
        $menuEntity = $this->getMenuRepository()->find($id);
        if (empty($menuEntity)) {
            $this->addFlash(
                CommonValues::FLASH_ERROR_TYPE,
                CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND
            );
            return $this->redirectToRoute('menu_admin_index');
        }
        try {
            $this->getMenuRepository()->down($menuEntity);
        } catch (ElementNotFoundException | \Throwable $exception) {
            $this->addFlash(
                CommonValues::FLASH_ERROR_TYPE,
                CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND
            );
            return $this->redirectToRoute('menu_admin_index');
        }

        $this->addFlash(
            CommonValues::FLASH_SUCCESS_TYPE,
            self::MENU_MOVED_DOWN_SUCCESS_MESSAGE
        );
        return $this->redirectToRoute('menu_admin_index');
    }

    public function up(int $id): Response
    {
        $menuEntity = $this->getMenuRepository()->find($id);
        if (empty($menuEntity)) {
            $this->addFlash(
                CommonValues::FLASH_ERROR_TYPE,
                CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND
            );
            return $this->redirectToRoute('menu_admin_index');
        }
        $this->getMenuRepository()->up($menuEntity);
        $this->addFlash(
            CommonValues::FLASH_SUCCESS_TYPE,
            self::MENU_MOVED_UP_SUCCESS_MESSAGE
        );
        return $this->redirectToRoute('menu_admin_index');
    }

    protected function getIndexRenderResponse(array $params): Response
    {
        return $this->render('@MartenaSoftMenu/admin/index.html.twig', $params);
    }

}

