<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category;

use FunTask\Domain\Category\Region;
use PHPUnit\Framework\TestCase;

final class RegionTest extends TestCase
{
    public function testFactoriesReturnExpectedValues(): void
    {
        self::assertSame('unspecified', Region::UNSPECIFIED()->getValue());
        self::assertSame('kg', Region::KG()->getValue());
        self::assertSame('ru', Region::RU()->getValue());
    }

    public function testEqualsAnyMatchesAnyProvidedRegion(): void
    {
        self::assertTrue(Region::KG()->equalsAny(Region::RU(), Region::KG()));
    }

    public function testConstructorIsNotPublic(): void
    {
        self::assertFalse((new \ReflectionMethod(Region::class, '__construct'))->isPublic());
    }
}
