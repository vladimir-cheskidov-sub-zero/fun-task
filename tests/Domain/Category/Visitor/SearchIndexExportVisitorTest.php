<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category\Visitor;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Tag;
use FunTask\Domain\Category\Visitor\SearchIndexExportVisitor;
use PHPUnit\Framework\TestCase;

final class SearchIndexExportVisitorTest extends TestCase
{
    public function testDocumentsCollectEligibleCategoriesInTraversalOrder(): void
    {
        $visitor = new SearchIndexExportVisitor();
        $rootCategory = $this->createCategory(
            'root',
            'Catalog',
            ['root'],
            [
                $this->createCategory('electronics', 'Electronics', ['searchable']),
                $this->createCategory('hidden', 'Hidden', ['searchable', 'hidden']),
                $this->createCategory('staff', 'Staff', ['searchable', 'restricted:staff-only']),
                $this->createCategory('wine', 'Wine', ['searchable', 'restricted:18plus', 'region:ru']),
                $this->createCategory('regional', 'Regional', ['searchable', 'region:kg', 'region:ru']),
                $this->createCategory('menu-only', 'Menu Only', ['menu']),
            ]
        );
        $rootCategory->accept($visitor);
        $documents = $visitor->documents();
        self::assertCount(3, $documents);
        self::assertSame('electronics', $documents[0]->id());
        self::assertFalse($documents[0]->adult());
        self::assertSame([], $documents[0]->regions());
        self::assertSame('wine', $documents[1]->id());
        self::assertTrue($documents[1]->adult());
        self::assertSame(['ru'], array_map(static function ($region): string {
            return $region->getValue();
        }, $documents[1]->regions()));
        self::assertSame('regional', $documents[2]->id());
        self::assertSame(['kg', 'ru'], array_map(static function ($region): string {
            return $region->getValue();
        }, $documents[2]->regions()));
    }
    public function testDocumentsStayEmptyWhenTreeHasNoEligibleCategories(): void
    {
        $visitor = new SearchIndexExportVisitor();
        $rootCategory = $this->createCategory(
            'root',
            'Catalog',
            ['root'],
            [
                $this->createCategory('hidden', 'Hidden', ['hidden']),
                $this->createCategory('staff', 'Staff', ['restricted:staff-only']),
            ]
        );
        $rootCategory->accept($visitor);
        self::assertSame([], $visitor->documents());
    }

    public function testRootCategoryIsNeverExportedEvenWhenSearchable(): void
    {
        $visitor = new SearchIndexExportVisitor();
        $rootCategory = $this->createCategory(
            'root',
            'Catalog',
            ['root', 'searchable'],
            [
                $this->createCategory('electronics', 'Electronics', ['searchable']),
            ]
        );

        $rootCategory->accept($visitor);

        self::assertCount(1, $visitor->documents());
        self::assertSame('electronics', $visitor->documents()[0]->id());
    }

    /**
     * @param string[] $tags
     * @param Category[] $children
     */
    private function createCategory(string $id, string $name, array $tags, array $children = []): Category
    {
        return new Category(
            new CategoryId($id),
            new CategoryName($name),
            new CategoryTags(array_map(static function (string $tag): Tag {
                return new Tag($tag);
            }, $tags)),
            new ChildCategories($children)
        );
    }
}
