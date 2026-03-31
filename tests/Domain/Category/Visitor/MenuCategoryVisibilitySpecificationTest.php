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
use FunTask\Domain\Category\Visitor\MenuCategoryVisibilitySpecification;
use PHPUnit\Framework\TestCase;

final class MenuCategoryVisibilitySpecificationTest extends TestCase
{
    public function testIsSatisfiedByReturnsTrueForRenderableMenuItem(): void
    {
        $specification = new MenuCategoryVisibilitySpecification(false, false, Region::KG());
        $category = $this->createCategory('visible', 'Visible', ['menu', 'region:kg']);

        self::assertTrue($specification->isSatisfiedBy($category));
    }
    public function testIsSatisfiedByRejectsCategoryWithoutMenuTag(): void
    {
        $specification = new MenuCategoryVisibilitySpecification(true, true, Region::UNSPECIFIED());
        $category = $this->createCategory('plain', 'Plain', []);

        self::assertFalse($specification->isSatisfiedBy($category));
    }
    public function testIsSatisfiedByRejectsHiddenOrRestrictedCategory(): void
    {
        $staffRestrictedSpecification = new MenuCategoryVisibilitySpecification(true, false, Region::UNSPECIFIED());
        $adultRestrictedSpecification = new MenuCategoryVisibilitySpecification(false, true, Region::UNSPECIFIED());

        self::assertFalse($staffRestrictedSpecification->isSatisfiedBy(
            $this->createCategory('hidden', 'Hidden', ['menu', 'hidden']),
        ));
        self::assertFalse($staffRestrictedSpecification->isSatisfiedBy(
            $this->createCategory('staff', 'Staff', ['menu', 'restricted:staff-only']),
        ));
        self::assertFalse($adultRestrictedSpecification->isSatisfiedBy(
            $this->createCategory('adult', 'Adult', ['menu', 'restricted:18plus']),
        ));
    }
    public function testIsSatisfiedByRejectsCategoryForDifferentRegion(): void
    {
        $specification = new MenuCategoryVisibilitySpecification(true, true, Region::KG());
        $category = $this->createCategory('ru', 'RU', ['menu', 'region:ru']);

        self::assertFalse($specification->isSatisfiedBy($category));
    }
    /**
     * @param string[] $tags
     */
    private function createCategory(string $id, string $name, array $tags): Category
    {
        return new Category(
            new CategoryId($id),
            new CategoryName($name),
            new CategoryTags(array_map(static function (string $tag): Tag {
                return new Tag($tag);
            }, $tags)),
            new ChildCategories([])
        );
    }
}
