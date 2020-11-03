<?php

namespace MartenaSoft\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Entity\Config;
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


}

