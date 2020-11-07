<?php

namespace MartenaSoft\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MartenaSoft\Common\Controller\AbstractAdminBaseController;
use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Entity\Config;
use MartenaSoft\Menu\Entity\ConfigSearch;
use MartenaSoft\Menu\Entity\Menu;
use MartenaSoft\Menu\Form\ConfigType;
use MartenaSoft\Menu\Form\SearchFormType;
use MartenaSoft\Menu\Repository\ConfigRepository;
use MartenaSoft\Menu\Repository\MenuRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminConfigController extends AbstractAdminBaseController
{
    public const CONFIG_SAVED_SUCCESS_MESSAGE = 'Config saved success';

    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private ConfigRepository $configRepository;

    public function __construct(
        EntityManagerInterface $entityManager, 
        LoggerInterface $logger, 
        ConfigRepository $configRepository
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->configRepository = $configRepository;
    }

    public function index(Request $request, PaginatorInterface $paginator, int $page = 1): Response
    {
        $itemQueryBuilder = $this
            ->configRepository
            ->getItemsQueryBuilder()
            ->orderBy('mc.id', 'DESC');

        if (!empty($searchWord = $request->query->getAlpha('w'))) {
            $itemQueryBuilder->andWhere("mc.name LIKE :name")->setParameter(':name', "%$searchWord%");
        }

        $itemQuery = $itemQueryBuilder->getQuery();
        $pagination = $paginator->paginate(
            $itemQuery,
            $page,
            CommonValues::ADMIN_PAGINATION_LIMIT
        );
        return $this->render('@MartenaSoftMenu/admin_config/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    public function save(Request $request, int $id = 0): Response
    {
        if (empty($id)) {
            $menuConfigEntity = new Config();
            $this->entityManager->persist($menuConfigEntity);
        } else {
            $menuConfigEntity = $this->configRepository->find($id);
        }

        $form = $this->createForm(ConfigType::class, $menuConfigEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                if ($form->get('isDefault')) {
                    $updated = $this->configRepository->setAllIsDefaultAsFalse();
                    $menuConfigEntity->setIsDefault(true);
                }

                $this->entityManager->flush($menuConfigEntity);
                $this->addFlash(CommonValues::FLASH_SUCCESS_TYPE, self::CONFIG_SAVED_SUCCESS_MESSAGE);
                return $this->redirectToRoute('menu_admin_config_index');
            } catch (\Throwable $exception) {
                $this->logger->error(CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE, [
                    'class' => __CLASS__,
                    'line' => $exception->getLine(),
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode()
                ]);
                $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                    CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE);
            }
        }

        return $this->render('@MartenaSoftMenu/admin_config/save.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function show(int $id = 0): Response
    {
        $configEntity = $this->configRepository->find($id);
        if (empty($configEntity)) {
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                            CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND);
            return $this->redirectToRoute('menu_admin_config_index');
        }
        return $this->render('@MartenaSoftMenu/admin_config/show.html.twig', [
            'configEntity' => $configEntity
        ]);
    }
    
    public function delete(int $id = 0): Response
    {
        $entity = $this->configRepository->find($id);
        if (empty($entity)) {
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                            CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND);
            return $this->redirectToRoute('menu_admin_config_index');
        }

        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } catch (\Throwable $exception) {
            $this->logger->error(CommonValues::ERROR_FORM_SAVE_LOGGER_MESSAGE, [
                'class' => __CLASS__,
                'func' => __FUNCTION__,
                'line' => __LINE__,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ]);
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                            CommonValues::ERROR_DELETE_SAVE_LOGGER_MESSAGE);
        }
        return $this->redirectToRoute('menu_admin_config_index');
    }
}
