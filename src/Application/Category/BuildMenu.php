<?php

declare(strict_types=1);

namespace FunTask\Application\Category;

use FunTask\Application\Vo\BuildMenuRegion;

final class BuildMenu
{
    private bool $adultEnabled;
    private string $path;
    private BuildMenuRegion $region;
    private bool $staffEnabled;
    public function __construct(string $path, bool $adultEnabled, BuildMenuRegion $region, bool $staffEnabled)
    {
        $this->adultEnabled = $adultEnabled;
        $this->path = trim($path);
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
    public function region(): BuildMenuRegion
    {
        return $this->region;
    }
    public function staffEnabled(): bool
    {
        return $this->staffEnabled;
    }
}
