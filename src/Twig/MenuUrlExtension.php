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
            new TwigFilter('mIsActive', [$this, 'isActive']),
        ];
    }

    public function getUrl(?MenuInterface $menuItem, string $prefix = '', string $postfix = ''): string
    {
        if (empty($menuItem)) {
            return "";
        }

        $result = $prefix .
            $menuItem->getPath() .
            (!empty($postfix) ? '/' : '').
            $postfix;
        return $this->clearUrl($result);
    }

    public function isActive(?MenuInterface $menu, string $activeUrl): bool
    {
        if (empty($url = substr($activeUrl, 0, strrpos($activeUrl, '?')))) {
            $url = $activeUrl;
        }
        return ($menu->getPath() == $url);
    }

    private function clearUrl(string $url): string
    {
        return preg_replace('/\/{2,}/', '', $url);
    }
}
