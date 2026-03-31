<?php

declare(strict_types=1);

namespace FunTask\Tests\Application\Service;

use FunTask\Application\Exception\CategoryTreePathCannotBeEmpty;
use FunTask\Application\Service\BuildMenu;
use FunTask\Application\Vo\CategoryRegion;
use PHPUnit\Framework\TestCase;

final class BuildMenuTest extends TestCase
{
    public function testConstructorNormalizesPath(): void
    {
        $query = new BuildMenu(' data/categories.json ', false, CategoryRegion::KG(), true);

        self::assertSame('data/categories.json', $query->path());
        self::assertFalse($query->adultEnabled());
        self::assertTrue($query->staffEnabled());
    }

    public function testConstructorRejectsEmptyPath(): void
    {
        $this->expectException(CategoryTreePathCannotBeEmpty::class);

        new BuildMenu('   ', false, CategoryRegion::KG(), false);
    }
}
