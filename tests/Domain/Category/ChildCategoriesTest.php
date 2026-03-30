<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Exception\CategoryChildWasNotFound;
use PHPUnit\Framework\TestCase;

final class ChildCategoriesTest extends TestCase
{
    public function testRemoveByIdRejectsUnknownChild(): void
    {
        $this->expectException(CategoryChildWasNotFound::class);

        (new ChildCategories([]))->removeById(new CategoryId('missing'));
    }

    public function testAddReturnsNewCollection(): void
    {
        $children = new ChildCategories([]);
        $updatedChildren = $children->add(
            new Category(
                new CategoryId('phones'),
                new CategoryName('Phones'),
                new CategoryTags([]),
                new ChildCategories([])
            )
        );

        self::assertCount(0, $children->all());
        self::assertCount(1, $updatedChildren->all());
        self::assertInstanceOf(Category::class, $updatedChildren->all()[0]);
    }
}
