<?php

declare(strict_types=1);

namespace FunTask\Application\Dto;

final class MenuItem
{
    /**
     * @var MenuItem[]
     */
    private array $children;
    private string $id;
    private string $name;
    /**
     * @param MenuItem[] $children
     */
    public function __construct(string $id, string $name, array $children)
    {
        $this->children = $children;
        $this->id = $id;
        $this->name = $name;
    }
    /**
     * @return MenuItem[]
     */
    public function children(): array
    {
        return $this->children;
    }
    public function id(): string
    {
        return $this->id;
    }
    public function name(): string
    {
        return $this->name;
    }
}
