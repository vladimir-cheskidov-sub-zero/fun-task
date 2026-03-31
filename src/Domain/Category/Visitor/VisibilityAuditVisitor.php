<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Visitor;

use FunTask\Domain\Category\Category;

final class VisibilityAuditVisitor implements CategoryVisitor
{
    private const REASON_HIDDEN = 'hidden';
    private const REASON_PROMO_WITHOUT_SEARCHABLE = 'promo_without_searchable';
    private const REASON_RESTRICTED_IN_MENU = 'restricted_in_menu';

    /**
     * @var string[]
     */
    private array $pathStack = [];
    /**
     * @var VisibilityAuditItem[]
     */
    private array $auditItems = [];

    public function enter(Category $category): void
    {
        $this->pathStack[] = $category->name()->toString();

        $reasons = $this->resolveReasons($category);
        if ($reasons === []) {
            return;
        }

        $this->auditItems[] = new VisibilityAuditItem(
            $category->id()->toString(),
            $category->name()->toString(),
            $this->pathStack,
            $reasons
        );
    }

    public function leave(Category $category): void
    {
        array_pop($this->pathStack);
    }

    /**
     * @return VisibilityAuditItem[]
     */
    public function auditItems(): array
    {
        return $this->auditItems;
    }

    /**
     * @return string[]
     */
    private function resolveReasons(Category $category): array
    {
        $reasons = [];

        if ($category->isHidden()) {
            $reasons[] = self::REASON_HIDDEN;
        }

        if ($category->isPromoted() && !$category->isSearchable()) {
            $reasons[] = self::REASON_PROMO_WITHOUT_SEARCHABLE;
        }

        if ($category->isRestricted() && $category->isMenuItem()) {
            $reasons[] = self::REASON_RESTRICTED_IN_MENU;
        }

        return $reasons;
    }
}
