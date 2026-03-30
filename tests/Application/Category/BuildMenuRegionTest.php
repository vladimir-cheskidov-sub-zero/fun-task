<?php

declare(strict_types=1);

namespace FunTask\Tests\Application\Category;

use FunTask\Application\Vo\BuildMenuRegion;
use FunTask\Domain\Category\Region;
use PHPUnit\Framework\TestCase;

final class BuildMenuRegionTest extends TestCase
{
    public function testToDomainRegionMapsSupportedValue(): void
    {
        $region = new BuildMenuRegion(BuildMenuRegion::KG);

        self::assertTrue($region->toDomainRegion()->equals(new Region(Region::KG)));
    }

    public function testToDomainRegionMapsUnspecifiedValue(): void
    {
        $region = new BuildMenuRegion(BuildMenuRegion::UNSPECIFIED);

        self::assertTrue($region->toDomainRegion()->equals(new Region(Region::UNSPECIFIED)));
    }
}
