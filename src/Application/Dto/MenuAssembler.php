<?php

declare(strict_types=1);

namespace FunTask\Application\Dto;

use FunTask\Domain\Category\Visitor\BuiltMenuItem;

final class MenuAssembler
{
    /**
     * @param BuiltMenuItem[] $menuItems
     */
    public function toMenuDto(array $menuItems): MenuDto
    {
        $menu = new MenuDto();
        $menu->items = $this->toItemDtos($menuItems);

        return $menu;
    }
    /**
     * @param BuiltMenuItem[] $menuItems
     *
     * @return MenuItemDto[]
     */
    private function toItemDtos(array $menuItems): array
    {
        $items = [];
        foreach ($menuItems as $menuItem) {
            $item = new MenuItemDto();
            $item->id = $menuItem->id();
            $item->name = $menuItem->name();
            $item->children = $this->toItemDtos($menuItem->children());
            $items[] = $item;
        }

        return $items;
    }
}
