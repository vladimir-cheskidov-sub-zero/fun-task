<?php

declare(strict_types=1);

namespace FunTask\Tests\Application\Category;

use FunTask\Application\Vo\CategoryRegion;
use FunTask\Domain\Category\Region;
use PHPUnit\Framework\TestCase;

final class BuildMenuRegionTest extends TestCase
{
    public function testToDomainRegionMapsSupportedValue(): void
    {
        $region = CategoryRegion::KG();

        self::assertTrue($region->toDomainRegion()->equals(Region::KG()));
    }

    public function testToDomainRegionMapsUnspecifiedValue(): void
    {
        $region = CategoryRegion::UNSPECIFIED();

        self::assertTrue($region->toDomainRegion()->equals(Region::UNSPECIFIED()));
    }

    public function testEqualsAnyMatchesAnyProvidedRegion(): void
    {
        self::assertTrue(CategoryRegion::KG()->equalsAny(CategoryRegion::RU(), CategoryRegion::KG()));
    }

    public function testConstructorIsNotPublic(): void
    {
        self::assertFalse((new \ReflectionMethod(CategoryRegion::class, '__construct'))->isPublic());
    }
}
