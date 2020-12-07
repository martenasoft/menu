<?php

namespace MartenaSoft\Menu\Twig;

use MartenaSoft\Menu\Entity\MenuInterface;
use MartenaSoft\Menu\Service\MenuUrlService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MenuUrlExtension extends AbstractExtension
{
    private MenuUrlService $menuUrlService;

    public function __construct(MenuUrlService $menuUrlService)
    {
        $this->menuUrlService = $menuUrlService;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('mUrl', [$this, 'getUrl']),
        ];
    }

    public function getUrl(?MenuInterface $menuItem, string $prefix = '', string $postfix = ''): string
    {
        if (empty($menuItem)) {
            return "";
        }

        $result = $prefix .
            $menuItem->getPath() .
            '/' .
            $menuItem->getTransliteratedUrl() .
            (!empty($postfix) ? '/' : '').
            $postfix;
        return $this->clearUrl($result);
    }

    private function clearUrl(string $url): string
    {
        return preg_replace('/\/{2,}/', '', $url);
    }
}
