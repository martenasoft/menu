<?php

namespace MartenaSoft\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Form\ConfigType;
use MartenaSoft\Menu\Repository\ConfigRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{
    public const CONFIG_SAVED_SUCCESS_MESSAGE = 'Config saved success';

    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function index(): Response
    {
        return $this->render('@MartenaSoftMenu/admin/index.html.twig');
    }

    public function config(Request $request, ConfigRepository $configRepository, ?string $name = null): Response
    {
        $menuConfigEntity = $configRepository->get($name);
        $form = $this->createForm(ConfigType::class, $menuConfigEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush($menuConfigEntity);
                $this->addFlash(CommonValues::ADD_FLASH_SUCCESS_TYPE, self::CONFIG_SAVED_SUCCESS_MESSAGE);
                return $this->redirectToRoute('menu_admin_config', ['name' => $name]);
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

        return $this->render('@MartenaSoftMenu/admin/config.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

