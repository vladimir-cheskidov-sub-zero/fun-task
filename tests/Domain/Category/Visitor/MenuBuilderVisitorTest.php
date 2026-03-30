<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category\Visitor;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Region;
use FunTask\Domain\Category\Tag;
use FunTask\Domain\Category\Visitor\MenuBuilderVisitor;
use PHPUnit\Framework\TestCase;

final class MenuBuilderVisitorTest extends TestCase
{
    public function testBuildsMenuInTreeOrderWithoutRenderingTechnicalRoot(): void
    {
        $rootCategory = $this->createCategory(
            'root',
            'Catalog',
            ['root'],
            [
                $this->createCategory(
                    'electronics',
                    'Electronics',
                    ['menu'],
                    [
                        $this->createCategory('smartphones', 'Smartphones', ['menu']),
                        $this->createCategory(
                            'accessories',
                            'Accessories',
                            [],
                            [
                                $this->createCategory('chargers', 'Chargers', ['menu']),
                            ]
                        ),
                    ]
                ),
                $this->createCategory('tv', 'TV', ['menu']),
            ]
        );
        $visitor = new MenuBuilderVisitor(false, false, Region::KG());
        $rootCategory->accept($visitor);
        $menuItems = $visitor->menuItems();
        self::assertSame(['electronics', 'tv'], array_map(static function ($item): string {
            return $item->id();
        }, $menuItems));
        self::assertSame(['smartphones'], array_map(static function ($item): string {
            return $item->id();
        }, $menuItems[0]->children()));
    }
    public function testFiltersHiddenRestrictedAndWrongRegionBranches(): void
    {
        $rootCategory = $this->createCategory(
            'root',
            'Catalog',
            ['root'],
            [
                $this->createCategory('visible-kg', 'Visible KG', ['menu', 'region:kg']),
                $this->createCategory('hidden', 'Hidden', ['menu', 'hidden']),
                $this->createCategory('adult', 'Adult', ['menu', 'restricted:18plus']),
                $this->createCategory('staff', 'Staff', ['menu', 'restricted:staff-only']),
                $this->createCategory('visible-ru', 'Visible RU', ['menu', 'region:ru']),
            ]
        );
        $visitor = new MenuBuilderVisitor(false, false, Region::KG());
        $rootCategory->accept($visitor);
        self::assertSame(['visible-kg'], array_map(static function ($item): string {
            return $item->id();
        }, $visitor->menuItems()));
    }
    public function testDoesNotFilterRegionalBranchesWhenRegionIsUnspecified(): void
    {
        $rootCategory = $this->createCategory(
            'root',
            'Catalog',
            ['root'],
            [
                $this->createCategory('visible-kg', 'Visible KG', ['menu', 'region:kg']),
                $this->createCategory('visible-ru', 'Visible RU', ['menu', 'region:ru']),
            ]
        );
        $visitor = new MenuBuilderVisitor(false, false, Region::UNSPECIFIED());
        $rootCategory->accept($visitor);
        self::assertSame(['visible-kg', 'visible-ru'], array_map(static function ($item): string {
            return $item->id();
        }, $visitor->menuItems()));
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
