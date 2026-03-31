<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Visitor;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\RestrictedVisibility;

final class SearchIndexExportVisitor implements CategoryVisitor
{
    /**
     * @var SearchIndexDocument[]
     */
    private array $documents = [];
    public function enter(Category $category): void
    {
        if (!$this->shouldExport($category)) {
            return;
        }
        $this->documents[] = new SearchIndexDocument(
            $category->id()->toString(),
            $category->name()->toString(),
            $category->hasRestrictedVisibility(RestrictedVisibility::ADULTS_ONLY()),
            $category->regions()
        );
    }
    public function leave(Category $category): void
    {
    }
    /**
     * @return SearchIndexDocument[]
     */
    public function documents(): array
    {
        return $this->documents;
    }
    private function shouldExport(Category $category): bool
    {
        if ($category->isRoot()) {
            return false;
        }

        if (!$category->isSearchable()) {
            return false;
        }
        if ($category->isHidden()) {
            return false;
        }
        return !$category->hasRestrictedVisibility(RestrictedVisibility::STAFF_ONLY());
    }
}
