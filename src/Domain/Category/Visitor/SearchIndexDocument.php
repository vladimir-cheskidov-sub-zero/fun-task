<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Visitor;

use FunTask\Domain\Category\Region;

final class SearchIndexDocument
{
    private bool $adult;
    private string $id;
    private string $name;
    /**
     * @var Region[]
     */
    private array $regions;
    /**
     * @param Region[] $regions
     */
    public function __construct(string $id, string $name, bool $adult, array $regions)
    {
        $this->adult = $adult;
        $this->id = $id;
        $this->name = $name;
        $this->regions = array_values($regions);
    }
    public function adult(): bool
    {
        return $this->adult;
    }
    public function id(): string
    {
        return $this->id;
    }
    public function name(): string
    {
        return $this->name;
    }
    /**
     * @return Region[]
     */
    public function regions(): array
    {
        return $this->regions;
    }
}
