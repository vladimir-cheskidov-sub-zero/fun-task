<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Visitor;

use FunTask\Domain\Category\Category;

final class MenuBuilderVisitor implements CategoryVisitor
{
    private MenuCategoryVisibilitySpecification $menuCategoryVisibilitySpecification;
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
    public function __construct(
        MenuCategoryVisibilitySpecification $menuCategoryVisibilitySpecification
    ) {
        $this->menuCategoryVisibilitySpecification = $menuCategoryVisibilitySpecification;
    }
    public function enter(Category $category): void
    {
        $parentBranchIsVisible = empty($this->branchVisibilityStack)
            ? true
            : $this->branchVisibilityStack[count($this->branchVisibilityStack) - 1];
        if ($category->isRoot()) {
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
        return $this->menuCategoryVisibilitySpecification->isSatisfiedBy($category);
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
}
