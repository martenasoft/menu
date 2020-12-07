<?php

namespace MartenaSoft\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MartenaSoft\Common\Controller\AbstractAdminBaseController;
use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Repository\ConfigRepository;
use MartenaSoft\Menu\Repository\MenuRepository;
use MartenaSoft\Menu\Service\SaveMenuItemServiceInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractMenuAdminController extends AbstractAdminBaseController
{
    public const MENU_SAVED_SUCCESS_MESSAGE = 'Config saved success';

    protected LoggerInterface $logger;
    protected MenuRepository $menuRepository;
    protected ConfigRepository $configRepository;
    protected SaveMenuItemServiceInterface $saveMenuItemService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher,
        MenuRepository $menuRepository,
        ConfigRepository $configRepository,
        SaveMenuItemServiceInterface $saveMenuItemService
    ) {
        parent::__construct($entityManager, $logger, $eventDispatcher);
        $this->menuRepository = $menuRepository;
        $this->configRepository = $configRepository;
        $this->saveMenuItemService = $saveMenuItemService;
    }

    protected function getConfigRepository(): ConfigRepository
    {
        return $this->configRepository;
    }

    protected function getMenuRepository(): MenuRepository
    {
        return $this->menuRepository;
    }

    public function delete(Request $request, Menu $menu): Response
    {
        if ($request->getMethod() != Request::METHOD_POST) {
            return $this->confirmDelete($request, $menu, $this->getReturnRouteName());
        } else {
            $post = $request->request->get('confirm_delete_form');
            $isSafeDelete = !empty($post['isSafeDelete']);

            try {
                $this->getEntityManager()->beginTransaction();
                $this->getMenuRepository()->delete($menu, $isSafeDelete);

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

        return $this->redirectToRoute($this->getReturnRouteName());
    }

    abstract protected function getReturnRouteName(): string;
}
