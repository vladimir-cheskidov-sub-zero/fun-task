<?php

declare(strict_types=1);

namespace FunTask\Application\Dto;

final class MenuItemDto
{
    /**
     * @var MenuItemDto[]
     */
    public array $children = [];
    public string $id;
    public string $name;
}
