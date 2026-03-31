<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Visitor;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\Region;
use FunTask\Domain\Category\RestrictedVisibility;

final class MenuCategoryVisibilitySpecification
{
    private bool $adultEnabled;
    private Region $region;
    private bool $staffEnabled;
    public function __construct(bool $adultEnabled, bool $staffEnabled, Region $region)
    {
        $this->adultEnabled = $adultEnabled;
        $this->region = $region;
        $this->staffEnabled = $staffEnabled;
    }
    public function isSatisfiedBy(Category $category): bool
    {
        if (!$category->isMenuItem()) {
            return false;
        }
        if ($category->isHidden()) {
            return false;
        }
        if (!$this->staffEnabled && $category->hasRestrictedVisibility(RestrictedVisibility::STAFF_ONLY())) {
            return false;
        }
        if (!$this->adultEnabled && $category->hasRestrictedVisibility(RestrictedVisibility::ADULTS_ONLY())) {
            return false;
        }
        return $category->isVisibleForRegion($this->region);
    }
}
