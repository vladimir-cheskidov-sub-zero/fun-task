<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category\Visitor;

use FunTask\Domain\Category\Exception\VisibilityAuditItemHasNoReasons;
use FunTask\Domain\Category\Visitor\VisibilityAuditItem;
use PHPUnit\Framework\TestCase;

final class VisibilityAuditItemTest extends TestCase
{
    public function testAccessorsReturnAssignedValues(): void
    {
        $item = new VisibilityAuditItem(
            'smartphones',
            'Smartphones',
            ['Catalog', 'Electronics', 'Smartphones'],
            ['hidden', 'restricted_in_menu']
        );

        self::assertSame('smartphones', $item->id());
        self::assertSame('Smartphones', $item->name());
        self::assertSame(['Catalog', 'Electronics', 'Smartphones'], $item->path());
        self::assertSame(['hidden', 'restricted_in_menu'], $item->reasons());
    }

    public function testConstructorRejectsEmptyReasons(): void
    {
        $this->expectException(VisibilityAuditItemHasNoReasons::class);

        new VisibilityAuditItem(
            'smartphones',
            'Smartphones',
            ['Catalog', 'Electronics', 'Smartphones'],
            []
        );
    }
}
