<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Visitor;

final class BuiltMenuItem
{
    /**
     * @var self[]
     */
    private array $children = [];
    private string $id;
    private string $name;
    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    public function addChild(self $child): void
    {
        $this->children[] = $child;
    }
    /**
     * @return self[]
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
