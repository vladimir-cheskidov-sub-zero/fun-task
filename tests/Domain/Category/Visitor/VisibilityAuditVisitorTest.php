<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category\Visitor;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Tag;
use FunTask\Domain\Category\Visitor\VisibilityAuditVisitor;
use PHPUnit\Framework\TestCase;

final class VisibilityAuditVisitorTest extends TestCase
{
    public function testAuditItemsCollectPathAndReasonsInTraversalOrder(): void
    {
        $visitor = new VisibilityAuditVisitor();
        $rootCategory = $this->createCategory(
            'root',
            'Catalog',
            ['root'],
            [
                $this->createCategory('visible', 'Visible', ['menu', 'searchable']),
                $this->createCategory('hidden', 'Hidden', ['hidden']),
                $this->createCategory('promo', 'Promo', ['promo']),
                $this->createCategory(
                    'restricted',
                    'Restricted',
                    ['menu', 'restricted:staff-only'],
                    [
                        $this->createCategory(
                            'nested',
                            'Nested',
                            ['hidden', 'promo', 'menu', 'restricted:18plus']
                        ),
                    ]
                ),
            ]
        );

        $rootCategory->accept($visitor);
        $auditItems = $visitor->auditItems();

        self::assertCount(4, $auditItems);
        self::assertSame('hidden', $auditItems[0]->id());
        self::assertSame(['Catalog', 'Hidden'], $auditItems[0]->path());
        self::assertSame(['hidden'], $auditItems[0]->reasons());
        self::assertSame('promo', $auditItems[1]->id());
        self::assertSame(['promo_without_searchable'], $auditItems[1]->reasons());
        self::assertSame('restricted', $auditItems[2]->id());
        self::assertSame(['restricted_in_menu'], $auditItems[2]->reasons());
        self::assertSame('nested', $auditItems[3]->id());
        self::assertSame(
            ['Catalog', 'Restricted', 'Nested'],
            $auditItems[3]->path()
        );
        self::assertSame(
            ['hidden', 'promo_without_searchable', 'restricted_in_menu'],
            $auditItems[3]->reasons()
        );
    }

    public function testAuditItemsStayEmptyWhenTreeHasNoProblems(): void
    {
        $visitor = new VisibilityAuditVisitor();
        $rootCategory = $this->createCategory(
            'root',
            'Catalog',
            ['root'],
            [
                $this->createCategory('visible', 'Visible', ['menu', 'searchable']),
            ]
        );

        $rootCategory->accept($visitor);

        self::assertSame([], $visitor->auditItems());
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
