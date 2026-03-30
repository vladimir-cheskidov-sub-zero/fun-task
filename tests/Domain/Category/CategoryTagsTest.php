<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category;

use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\Exception\CategoryAlreadyHasTag;
use FunTask\Domain\Category\Exception\CategoryDoesNotHaveTag;
use FunTask\Domain\Category\Tag;
use PHPUnit\Framework\TestCase;

final class CategoryTagsTest extends TestCase
{
    public function testConstructorRejectsDuplicates(): void
    {
        $this->expectException(CategoryAlreadyHasTag::class);

        new CategoryTags([new Tag('menu'), new Tag('menu')]);
    }

    public function testAddReturnsNewCollection(): void
    {
        $tags = new CategoryTags([new Tag('menu')]);
        $updatedTags = $tags->add(new Tag('promo'));

        self::assertCount(1, $tags->all());
        self::assertCount(2, $updatedTags->all());
    }

    public function testRemoveRejectsUnknownTag(): void
    {
        $this->expectException(CategoryDoesNotHaveTag::class);

        (new CategoryTags([new Tag('menu')]))->remove(new Tag('promo'));
    }
}
