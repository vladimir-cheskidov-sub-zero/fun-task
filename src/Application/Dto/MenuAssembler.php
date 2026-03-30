<?php

declare(strict_types=1);

namespace FunTask\Application\Dto;

use FunTask\Domain\Category\Visitor\BuiltMenuItem;

final class MenuAssembler
{
    /**
     * @param BuiltMenuItem[] $menuItems
     */
    public function assemble(array $menuItems): Menu
    {
        return new Menu($this->assembleItems($menuItems));
    }
    /**
     * @param BuiltMenuItem[] $menuItems
     *
     * @return MenuItem[]
     */
    private function assembleItems(array $menuItems): array
    {
        $items = [];
        foreach ($menuItems as $menuItem) {
            $items[] = new MenuItem(
                $menuItem->id(),
                $menuItem->name(),
                $this->assembleItems($menuItem->children())
            );
        }
        return $items;
    }
}
