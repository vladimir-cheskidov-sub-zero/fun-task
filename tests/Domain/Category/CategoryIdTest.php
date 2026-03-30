<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category;

use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\Exception\CategoryIdCannotBeEmpty;
use PHPUnit\Framework\TestCase;

final class CategoryIdTest extends TestCase
{
    public function testConstructorRejectsEmptyValue(): void
    {
        $this->expectException(CategoryIdCannotBeEmpty::class);

        new CategoryId('   ');
    }

    public function testEqualsComparesNormalizedValue(): void
    {
        self::assertTrue((new CategoryId(' phones '))->equals(new CategoryId('phones')));
    }
}
