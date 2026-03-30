<?php

declare(strict_types=1);

namespace FunTask\Application\Dto;

final class Menu
{
    /**
     * @var MenuItem[]
     */
    private array $items;
    /**
     * @param MenuItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }
    /**
     * @return MenuItem[]
     */
    public function items(): array
    {
        return $this->items;
    }
}
