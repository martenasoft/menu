<?php

namespace MartenaSoft\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MartenaSoft\Common\Controller\AbstractAdminBaseController;
use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Entity\Config;
use MartenaSoft\Menu\Form\ConfigType;
use MartenaSoft\Menu\Repository\ConfigRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminConfigController extends AbstractAdminBaseController
{
    public const CONFIG_SAVED_SUCCESS_MESSAGE = 'Config saved success';

    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function index(
        Request $request,
        ConfigRepository $configRepository,
        PaginatorInterface $paginator,
        int $page = 1): Response
    {
        $itemQuery = $configRepository->getItemsQueryBuilder()->getQuery();
        $pagination = $paginator->paginate(
            $itemQuery,
            $page,
            CommonValues::ADMIN_PAGINATION_LIMIT
        );

        return $this->render('@MartenaSoftMenu/admin_config/index.html.twig', ['pagination' => $pagination]);
    }

    public function save(Request $request, ConfigRepository $configRepository, int $id = 0): Response
    {
        if (empty($id)) {
            $menuConfigEntity = new Config();
            $this->entityManager->persist($menuConfigEntity);
        } else {
            $menuConfigEntity = $configRepository->find($id);
        }

        $form = $this->createForm(ConfigType::class, $menuConfigEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush($menuConfigEntity);
                $this->addFlash(CommonValues::ADD_FLASH_SUCCESS_TYPE, self::CONFIG_SAVED_SUCCESS_MESSAGE);
                return $this->redirectToRoute('menu_admin_config_index');
            } catch (\Throwable $exception) {
                $this->logger->error(CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE, [
                    'class' => __CLASS__,
                    'func' => __FUNCTION__,
                    'line' => __LINE__,
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode()
                ]);
                $this->addFlash(CommonValues::ADD_FLASH_ERROR_TYPE,
                    CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE);
            }
        }

        return $this->render('@MartenaSoftMenu/admin_config/save.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
