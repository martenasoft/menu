<?php

namespace MartenaSoft\Menu\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MartenaSoft\Common\Controller\AbstractAdminBaseController;
use MartenaSoft\Common\Library\CommonValues;
use MartenaSoft\Menu\Repository\ConfigRepository;
use MartenaSoft\Menu\Repository\MenuRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractMenuAdminController extends AbstractAdminBaseController
{
    public const MENU_SAVED_SUCCESS_MESSAGE = 'Config saved success';

    private LoggerInterface $logger;
    private MenuRepository $menuRepository;
    private ConfigRepository $configRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        MenuRepository $menuRepository,
        ConfigRepository $configRepository
    ) {
        parent::__construct($entityManager, $logger);
        $this->menuRepository = $menuRepository;
        $this->configRepository = $configRepository;
    }

    protected function getConfigRepository(): ConfigRepository
    {
        return $this->configRepository;
    }

    protected function getMenuRepository(): MenuRepository
    {
        return $this->menuRepository;
    }


}
