<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category;

use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\Exception\CategoryNameCannotBeEmpty;
use PHPUnit\Framework\TestCase;

final class CategoryNameTest extends TestCase
{
    public function testConstructorRejectsEmptyValue(): void
    {
        $this->expectException(CategoryNameCannotBeEmpty::class);

        new CategoryName('   ');
    }

    public function testEqualsComparesNormalizedValue(): void
    {
        self::assertTrue((new CategoryName(' Electronics '))->equals(new CategoryName('Electronics')));
    }
}
