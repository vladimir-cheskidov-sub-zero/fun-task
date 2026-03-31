<?php

declare(strict_types=1);

namespace FunTask\Application\Service;

use FunTask\Application\Exception\CategoryTreePathCannotBeEmpty;
use FunTask\Application\Vo\CategoryRegion;
use FunTask\Domain\Category\Region;

final class BuildMenu
{
    private bool $adultEnabled;
    private string $path;
    private CategoryRegion $region;
    private bool $staffEnabled;
    /**
     * @throws CategoryTreePathCannotBeEmpty
     */
    public function __construct(string $path, bool $adultEnabled, CategoryRegion $region, bool $staffEnabled)
    {
        $normalizedPath = trim($path);
        if ($normalizedPath === '') {
            throw CategoryTreePathCannotBeEmpty::becauseValueIsEmpty();
        }

        $this->adultEnabled = $adultEnabled;
        $this->path = $normalizedPath;
        $this->region = $region;
        $this->staffEnabled = $staffEnabled;
    }
    public function adultEnabled(): bool
    {
        return $this->adultEnabled;
    }
    public function path(): string
    {
        return $this->path;
    }
    public function region(): Region
    {
        return $this->region->toDomainRegion();
    }
    public function staffEnabled(): bool
    {
        return $this->staffEnabled;
    }
}
