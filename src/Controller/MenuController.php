<?php

namespace MartenaSoft\Menu\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('@MartenaSoftMenu/menu/index.html.twig');
    }
}