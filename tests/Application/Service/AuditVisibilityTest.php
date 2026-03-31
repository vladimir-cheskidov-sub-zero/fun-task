<?php

declare(strict_types=1);

namespace FunTask\Tests\Application\Service;

use FunTask\Application\Exception\CategoryTreePathCannotBeEmpty;
use FunTask\Application\Service\AuditVisibility;
use PHPUnit\Framework\TestCase;

final class AuditVisibilityTest extends TestCase
{
    public function testConstructorNormalizesPath(): void
    {
        $query = new AuditVisibility(' data/categories.json ');

        self::assertSame('data/categories.json', $query->path());
    }

    public function testConstructorRejectsEmptyPath(): void
    {
        $this->expectException(CategoryTreePathCannotBeEmpty::class);

        new AuditVisibility('   ');
    }
}
