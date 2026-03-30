<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Visitor;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\Region;
use FunTask\Domain\Category\RestrictedVisibility;

final class MenuBuilderVisitor implements CategoryVisitor
{
    private const TAG_HIDDEN = 'hidden';
    private const TAG_MENU = 'menu';
    private const TAG_ROOT = 'root';
    private bool $adultEnabled;
    private Region $region;
    private bool $staffEnabled;
    /**
     * @var array<int, bool>
     */
    private array $branchVisibilityStack = [];
    /**
     * @var array<int, BuiltMenuItem|null>
     */
    private array $renderedItemsStack = [];
    /**
     * @var BuiltMenuItem[]
     */
    private array $menuItems = [];
    public function __construct(bool $adultEnabled, bool $staffEnabled, Region $region)
    {
        $this->adultEnabled = $adultEnabled;
        $this->region = $region;
        $this->staffEnabled = $staffEnabled;
    }
    public function enter(Category $category): void
    {
        $parentBranchIsVisible = empty($this->branchVisibilityStack)
            ? true
            : $this->branchVisibilityStack[count($this->branchVisibilityStack) - 1];
        if ($this->hasTagValue($category, self::TAG_ROOT)) {
            $this->branchVisibilityStack[] = true;
            $this->renderedItemsStack[] = null;
            return;
        }
        if (!$parentBranchIsVisible || !$this->shouldRender($category)) {
            $this->branchVisibilityStack[] = false;
            $this->renderedItemsStack[] = null;
            return;
        }
        $menuItem = new BuiltMenuItem($category->id()->toString(), $category->name()->toString());
        $parentItem = $this->currentParentItem();
        if (null === $parentItem) {
            $this->menuItems[] = $menuItem;
        } else {
            $parentItem->addChild($menuItem);
        }
        $this->branchVisibilityStack[] = true;
        $this->renderedItemsStack[] = $menuItem;
    }
    public function leave(Category $category): void
    {
        array_pop($this->branchVisibilityStack);
        array_pop($this->renderedItemsStack);
    }
    /**
     * @return BuiltMenuItem[]
     */
    public function menuItems(): array
    {
        return $this->menuItems;
    }
    private function shouldRender(Category $category): bool
    {
        if (!$this->hasTagValue($category, self::TAG_MENU)) {
            return false;
        }
        if ($this->hasTagValue($category, self::TAG_HIDDEN)) {
            return false;
        }
        if (!$this->staffEnabled && $this->hasTagValue($category, sprintf('restricted:%s', RestrictedVisibility::STAFF_ONLY))) {
            return false;
        }
        if (!$this->adultEnabled && $this->hasTagValue($category, sprintf('restricted:%s', RestrictedVisibility::ADULTS_ONLY))) {
            return false;
        }
        return $this->isVisibleForRegion($category);
    }
    private function isVisibleForRegion(Category $category): bool
    {
        if ($this->region->equals(new Region(Region::UNSPECIFIED))) {
            return true;
        }
        $allowedRegionTag = sprintf('region:%s', $this->region->getValue());
        foreach ($category->tags() as $tag) {
            if (strpos($tag->value(), 'region:') !== 0) {
                continue;
            }
            if ($tag->value() !== $allowedRegionTag) {
                return false;
            }
        }
        return true;
    }
    private function currentParentItem(): ?BuiltMenuItem
    {
        if (empty($this->renderedItemsStack)) {
            return null;
        }
        $parentItem = $this->renderedItemsStack[count($this->renderedItemsStack) - 1];
        if (null === $parentItem) {
            return null;
        }
        return $parentItem;
    }
    private function hasTagValue(Category $category, string $tagValue): bool
    {
        foreach ($category->tags() as $tag) {
            if ($tag->value() === $tagValue) {
                return true;
            }
        }
        return false;
    }
}
