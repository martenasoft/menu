<?php

namespace MartenaSoft\Menu\Service;

use MartenaSoft\Menu\Entity\MenuInterface;
use MartenaSoft\Menu\Repository\MenuRepository;

class MenuUrlService
{
    private MenuRepository $menuRepository;
    public function __construct(MenuRepository $menuRepository)
    {
        $this->menuRepository = $menuRepository;
    }

    public function urlPathFromItem(?MenuInterface $menu, string $slider = "/"): ?string
    {
        $queryBuilder = $this->menuRepository->getParentsByItemQueryBuilder($menu);
        $items = $queryBuilder->orderBy(MenuRepository::getAlias().".rgt", "DESC")->getQuery()->getResult();
        $result = "";
        if (!empty($items)) {
            foreach ($items as $item) {
                $result .= $slider . $item->getTransliteratedUrl();
            }
        }
        $result = $result . $slider . $menu->getTransliteratedUrl();
        return $result;
    }
}
