<?php

namespace MartenaSoft\Menu\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MartenaSoft\Common\Controller\AbstractAdminBaseController;
use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Entity\Config;
use MartenaSoft\Menu\Entity\ConfigSearch;
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
    public const CONFIG_DELETE_ERROR_DEFAULT_MESSAGE = "You can't delete default configure.";
    public const CONFIG_DELETE_ERROR_FOREIGN_MESSAGE = "This configurations used in menus";


    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private ConfigRepository $configRepository;
    private MenuRepository $menuRepository;

    public function __construct(
        EntityManagerInterface $entityManager, 
        LoggerInterface $logger, 
        ConfigRepository $configRepository,
        MenuRepository $menuRepository
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->configRepository = $configRepository;
        $this->menuRepository = $menuRepository;
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
            $count = $this->configRepository->count(["isDefault" => true]);
            $menuConfigEntity->setIsDefault($count == 0);
            $this->entityManager->persist($menuConfigEntity);
        } else {
            $menuConfigEntity = $this->configRepository->find($id);
        }

        $form = $this->createForm(ConfigType::class, $menuConfigEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($form->get('isDefault')) {
                    $this->configRepository->setAllIsDefaultAsFalse();
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

    public function view(int $id = 0): Response
    {
        $configEntity = $this->configRepository->find($id);
        if (empty($configEntity)) {
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                            CommonValues::ERROR_ENTITY_RECORD_NOT_FOUND);
            return $this->redirectToRoute('menu_admin_config_index');
        }

        $allRoots = $this
            ->menuRepository
            ->getAllRootsQueryBuilder()
            ->andWhere("m.config=:config")
            ->setParameter("config", $configEntity)
            ->getQuery()
            ->getResult()
        ;

        return $this->render('@MartenaSoftMenu/admin_config/view.html.twig', [
            'configEntity' => $configEntity,
            'allRoots' => $allRoots
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

        if ($entity->isDefault()) {
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                            self::CONFIG_DELETE_ERROR_DEFAULT_MESSAGE);
            return $this->redirectToRoute('menu_admin_config_index');
        }

        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } catch (ForeignKeyConstraintViolationException $exception) {
            $this->addFlash(CommonValues::FLASH_ERROR_TYPE,
                            self::CONFIG_DELETE_ERROR_FOREIGN_MESSAGE);
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
