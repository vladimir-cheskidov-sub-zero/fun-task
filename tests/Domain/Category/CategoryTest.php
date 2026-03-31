<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Exception\CategoryAlreadyHasTag;
use FunTask\Domain\Category\Exception\CategoryCannotContainItself;
use FunTask\Domain\Category\Exception\CategoryChildWasNotFound;
use FunTask\Domain\Category\Exception\CategoryNameIsAlreadyUsed;
use FunTask\Domain\Category\Exception\DuplicateCategoryTreeId;
use FunTask\Domain\Category\Region;
use FunTask\Domain\Category\RestrictedVisibility;
use FunTask\Domain\Category\Tag;
use PHPUnit\Framework\TestCase;

final class CategoryTest extends TestCase
{
    public function testRenameReturnsNewCategory(): void
    {
        $category = $this->createCategory('electronics', 'Electronics');
        $renamedCategory = $category->rename(new CategoryName('Devices'));

        self::assertSame('Electronics', $category->name()->toString());
        self::assertSame('Devices', $renamedCategory->name()->toString());
    }

    public function testRenameRejectsSameName(): void
    {
        $this->expectException(CategoryNameIsAlreadyUsed::class);

        $this->createCategory('electronics', 'Electronics')->rename(new CategoryName('Electronics'));
    }

    public function testAddTagReturnsNewCategory(): void
    {
        $category = $this->createCategory('electronics', 'Electronics', [new Tag('menu')]);
        $updatedCategory = $category->addTag(new Tag('promo'));

        self::assertCount(1, $category->tags()->all());
        self::assertCount(2, $updatedCategory->tags()->all());
    }

    public function testAddTagRejectsDuplicate(): void
    {
        $this->expectException(CategoryAlreadyHasTag::class);

        $this->createCategory('electronics', 'Electronics', [new Tag('menu')])->addTag(new Tag('menu'));
    }

    public function testIsPromotedReturnsTrueOnlyForPromoTaggedCategory(): void
    {
        self::assertTrue($this->createCategory('promo', 'Promo', [new Tag('promo')])->isPromoted());
        self::assertFalse($this->createCategory('visible', 'Visible', [new Tag('menu')])->isPromoted());
    }

    public function testIsSearchableReturnsTrueOnlyForSearchableTaggedCategory(): void
    {
        self::assertTrue($this->createCategory('search', 'Search', [new Tag('searchable')])->isSearchable());
        self::assertFalse($this->createCategory('visible', 'Visible', [new Tag('menu')])->isSearchable());
    }

    public function testIsRestrictedReturnsTrueOnlyForRestrictedTaggedCategory(): void
    {
        self::assertTrue($this->createCategory('staff', 'Staff', [new Tag('restricted:staff-only')])->isRestricted());
        self::assertFalse($this->createCategory('visible', 'Visible', [new Tag('menu')])->isRestricted());
    }

    public function testIsRootReturnsTrueOnlyForRootTaggedCategory(): void
    {
        self::assertTrue($this->createCategory('root', 'Catalog', [new Tag('root')])->isRoot());
        self::assertFalse($this->createCategory('electronics', 'Electronics', [new Tag('menu')])->isRoot());
    }
    public function testIsMenuItemReturnsTrueOnlyForMenuTaggedCategory(): void
    {
        self::assertTrue($this->createCategory('electronics', 'Electronics', [new Tag('menu')])->isMenuItem());
        self::assertFalse($this->createCategory('electronics', 'Electronics', [new Tag('hidden')])->isMenuItem());
    }
    public function testIsHiddenReturnsTrueOnlyForHiddenTaggedCategory(): void
    {
        self::assertTrue($this->createCategory('hidden', 'Hidden', [new Tag('hidden')])->isHidden());
        self::assertFalse($this->createCategory('visible', 'Visible', [new Tag('menu')])->isHidden());
    }

    public function testHasRestrictedVisibilityReturnsTrueOnlyForMatchingRestriction(): void
    {
        $category = $this->createCategory('staff', 'Staff', [new Tag('restricted:staff-only')]);

        self::assertTrue($category->hasRestrictedVisibility(RestrictedVisibility::STAFF_ONLY()));
        self::assertFalse($category->hasRestrictedVisibility(RestrictedVisibility::ADULTS_ONLY()));
    }

    public function testIsVisibleForRegionReturnsTrueForMatchingRegion(): void
    {
        $category = $this->createCategory('kg', 'KG', [new Tag('region:kg')]);

        self::assertTrue($category->isVisibleForRegion(Region::KG()));
        self::assertFalse($category->isVisibleForRegion(Region::RU()));
    }

    public function testIsVisibleForRegionReturnsTrueWhenRegionIsUnspecified(): void
    {
        $category = $this->createCategory('kg', 'KG', [new Tag('region:kg')]);

        self::assertTrue($category->isVisibleForRegion(Region::UNSPECIFIED()));
    }

    public function testAddChildReturnsNewCategory(): void
    {
        $category = $this->createCategory('root', 'Catalog');
        $child = $this->createCategory('phones', 'Phones');
        $updatedCategory = $category->addChild($child);

        self::assertCount(0, $category->children()->all());
        self::assertCount(1, $updatedCategory->children()->all());
    }

    public function testAddChildRejectsOwnIdentifier(): void
    {
        $this->expectException(CategoryCannotContainItself::class);

        $category = $this->createCategory('root', 'Catalog');
        $category->addChild($this->createCategory('root', 'Catalog child'));
    }

    public function testConstructorRejectsDuplicateTreeIdentifier(): void
    {
        $this->expectException(DuplicateCategoryTreeId::class);

        new Category(
            new CategoryId('root'),
            new CategoryName('Catalog'),
            new CategoryTags([]),
            new ChildCategories(
                [
                    $this->createCategory(
                        'electronics',
                        'Electronics',
                        [],
                        [
                            $this->createCategory('phones', 'Phones'),
                        ]
                    ),
                    $this->createCategory('phones', 'Duplicate phones'),
                ]
            )
        );
    }

    public function testRemoveChildRejectsUnknownIdentifier(): void
    {
        $this->expectException(CategoryChildWasNotFound::class);

        $this->createCategory('root', 'Catalog')->removeChild(new CategoryId('missing'));
    }

    /**
     * @param Tag[] $tags
     * @param Category[] $children
     */
    private function createCategory(string $id, string $name, array $tags = [], array $children = []): Category
    {
        return new Category(
            new CategoryId($id),
            new CategoryName($name),
            new CategoryTags($tags),
            new ChildCategories($children)
        );
    }
}
