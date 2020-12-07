<?php

namespace MartenaSoft\Menu\Controller;

use App\Kernel;
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
use MartenaSoft\Menu\Service\SaveMenuItemServiceInterface;
use MartenaSoft\NestedSets\Exception\NestedSetsMoveUnderSelfException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class AdminController extends AbstractMenuAdminController
{
    public const MENU_MOVED_DOWN_SUCCESS_MESSAGE = 'Menu moved down';
    public const MENU_MOVED_UP_SUCCESS_MESSAGE = 'Menu moved up';

    public function index(Request $request, PaginatorInterface $paginator, int $page = 1): Response
    {
        $itemQueryBuilder = $this
            ->getMenuRepository()
            ->getAllQueryBuilder();

        $w = $request->query->get(CommonValues::SEARCH_URL_PARAM_NAME);

        if (!empty($w)) {
            $itemQueryBuilder->andWhere("m.name LIKE :w")->setParameter("w", "%{$w}%");
        }

        $itemQuery = $itemQueryBuilder->getQuery();
        $pagination = $paginator->paginate(
            $itemQuery,
            $page,
            CommonValues::ADMIN_PAGINATION_LIMIT,
            ['distinct' => false]
        );
        return $this->render('@MartenaSoftMenu/admin/index.html.twig', ['pagination' => $pagination]);
    }

    public function create(Request $request, Menu $parent): Response
    {
        $formView = null;
        try {
            $menu = new Menu();
            $menu->setParentId($parent->getId());
            $menu->setTree($parent->getTree());
            $form = $this->save($request, $menu, $parent);
            $formView = $form->createView();
            if ($form->isSubmitted() && $form->isValid()) {
                $this->addFlash(CommonValues::FLASH_SUCCESS_TYPE, self::MENU_SAVED_SUCCESS_MESSAGE);
                return $this->redirectToRoute('menu_admin_index');
            }

        } catch (\Throwable $exception) {
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
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE, CommonValues::FLASH_ERROR_SYSTEM_MESSAGE);
        }

        return $this->render('@MartenaSoftMenu/admin/create.html.twig', [
            'form' => $formView,
            'parent' => $parent
        ]);
    }

    public function edit(Request $request, Menu $menu): Response
    {
        $formView = null;

        try {
            $parent = $this->getMenuRepository()->find($menu->getParentId());

            $form = $this->save($request, $menu, $parent);
            $formView = $form->createView();
            if ($form->isSubmitted() && $form->isValid()) {
                $this->addFlash(CommonValues::FLASH_SUCCESS_TYPE, self::MENU_SAVED_SUCCESS_MESSAGE);
                return $this->redirectToRoute('menu_admin_index');
            }
        } catch (NestedSetsMoveUnderSelfException $exception) {
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                NestedSetsMoveUnderSelfException::MESSAGE);
            return $this->redirectToRoute('menu_admin_index');
        } catch (\Throwable $exception) {
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
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE, CommonValues::FLASH_ERROR_SYSTEM_MESSAGE);
        }


        return $this->render('@MartenaSoftMenu/admin/edit.html.twig', [
            'form' => $formView,
            'menu' => $menu
        ]);
    }

    public function down(Request $request, Menu $menuEntity): Response
    {
        if (empty($menuEntity)) {
            $this->addFlash(
                CommonValues::FLASH_ERROR_TYPE,
                CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND
            );
            return $this->redirectToRoute('menu_admin_index', $this->getActiveParams($request));
        }
        try {
            $this->getMenuRepository()->upDown($menuEntity, false);
        } catch (ElementNotFoundException | \Throwable $exception) {
            $this->addFlash(
                CommonValues::FLASH_ERROR_TYPE,
                CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND
            );
            return $this->redirectToRoute('menu_admin_index', $this->getActiveParams($request));
        }

        $this->addFlash(
            CommonValues::FLASH_SUCCESS_TYPE,
            self::MENU_MOVED_DOWN_SUCCESS_MESSAGE
        );
        return $this->redirectToRoute('menu_admin_index', $this->getActiveParams($request));
    }

    public function up(Request $request, Menu $menuEntity): Response
    {
        if (empty($menuEntity)) {
            $this->addFlash(
                CommonValues::FLASH_ERROR_TYPE,
                CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND
            );
            return $this->redirectToRoute('menu_admin_index', $this->getActiveParams($request));
        }
        $this->getMenuRepository()->upDown($menuEntity);
        $this->addFlash(
            CommonValues::FLASH_SUCCESS_TYPE,
            self::MENU_MOVED_UP_SUCCESS_MESSAGE
        );

        return $this->redirectToRoute('menu_admin_index', $this->getActiveParams($request));
    }

    protected function getReturnRouteName(): string
    {
        return 'menu_admin_index';
    }

    private function getActiveParams(Request $request): array
    {
        $act_ = $request->query->getInt(CommonValues::ACTIVE_URL_PARAM_NAME);

        if (!empty($act_)) {
            return [CommonValues::ACTIVE_URL_PARAM_NAME => $act_];
        }
        return [];
    }

    private function save(
        Request $request,
        SaveMenuItemServiceInterface $saveMenuItemService,
        Menu $menuEntity,
        ?Menu $parent = null
    ): FormInterface {

        $form = $this->createForm(MenuType::class, $menuEntity, ['menu' => $menuEntity]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $defaultConfig = null;

            if ($form->getData()->getParentId() != $parent->getId()) {

                if ($form->getData()->getParentId() > 0) {
                    $parent = $this->getMenuRepository()->find($form->getData()->getParentId());
                } else {
                    $defaultConfig = $this->getConfigRepository()->getOrCreateDefault();
                }
            }

            try {
                $saveMenuItemService->save($menuEntity, $parent, $defaultConfig);

                $this->getEntityManager()->beginTransaction();
                if ($menuEntity->getId() === null) {
                    $this->getMenuRepository()->create($menuEntity, $parent);
                } else {



                }
                $this->getEntityManager()->flush($menuEntity);
                $this->getEntityManager()->commit();

            } catch (\Throwable $exception) {
                $this->getEntityManager()->rollback();
                throw $exception;
            }
        }
        return $form;
    }
}

