<?php

namespace MartenaSoft\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MartenaSoft\Common\Exception\ElementNotFoundException;
use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Form\MenuType;
use MartenaSoft\Menu\Repository\MenuRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{
    public const MENU_SAVED_SUCCESS_MESSAGE = 'Config saved success';
    public const MENU_MOVED_DOWN_SUCCESS_MESSAGE = 'Menu moved down';
    public const MENU_MOVED_UP_SUCCESS_MESSAGE = 'Menu moved up';

    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private MenuRepository $menuRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        MenuRepository $menuRepository
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->menuRepository = $menuRepository;
    }

    public function index(PaginatorInterface $paginator, int $page = 1): Response
    {
        $itemQuery = $this->menuRepository->getAllQueryBuilder()->getQuery();
        $pagination = $paginator->paginate(
            $itemQuery,
            $page,
            CommonValues::ADMIN_PAGINATION_LIMIT
        );
        return $this->render('@MartenaSoftMenu/admin/index.html.twig', ['pagination' => $pagination]);
    }

    public function save(Request $request, int $id = 0): Response
    {
        if (empty($id)) {
            $menuEntity = new Menu();
            $this->entityManager->persist($menuEntity);
        } else {
            $menuEntity = $this->menuRepository->find($id);
        }

        $form = $this->createForm(MenuType::class, $menuEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush($menuEntity);
                $this->addFlash(CommonValues::FLASH_SUCCESS_TYPE, self::CONFIG_SAVED_SUCCESS_MESSAGE);
                return $this->redirectToRoute('menu_admin_index');
            } catch (\Throwable $exception) {
                $this->logger->error(CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE, [
                    'class' => __CLASS__,
                    'func' => __FUNCTION__,
                    'line' => __LINE__,
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode()
                ]);
                $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                                CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE);
            }
        }

        return $this->render('@MartenaSoftMenu/admin/save.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function down(int $id): Response
    {
        $menuEntity = $this->menuRepository->find($id);
        if (empty($menuEntity)) {
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                            CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND);
            return $this->redirectToRoute('menu_admin_index');
        }
        try {
            $this->menuRepository->down($menuEntity);
        } catch (ElementNotFoundException | \Throwable $exception) {

            $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                            CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND);
            return $this->redirectToRoute('menu_admin_index');
        }

        $this->addFlash(CommonValues::FLASH_SUCCESS_TYPE,
                        self::MENU_MOVED_DOWN_SUCCESS_MESSAGE);
        return $this->redirectToRoute('menu_admin_index');
    }

    public function up(int $id): Response
    {
        $menuEntity = $this->menuRepository->find($id);
        if (empty($menuEntity)) {
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                            CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND);
            return $this->redirectToRoute('menu_admin_index');
        }
        $this->menuRepository->up($menuEntity);
        $this->addFlash(CommonValues::FLASH_SUCCESS_TYPE,
                        self::MENU_MOVED_UP_SUCCESS_MESSAGE);
        return $this->redirectToRoute('menu_admin_index');
    }

}

