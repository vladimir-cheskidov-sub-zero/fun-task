<?php

declare(strict_types=1);

namespace FunTask\Tests\Application\Dto;

use FunTask\Application\Dto\VisibilityAuditAssembler;
use FunTask\Application\Dto\VisibilityAuditDto;
use FunTask\Domain\Category\Visitor\VisibilityAuditItem;
use PHPUnit\Framework\TestCase;

final class VisibilityAuditAssemblerTest extends TestCase
{
    public function testToVisibilityAuditDtoBuildsDtoCollection(): void
    {
        $assembler = new VisibilityAuditAssembler();

        $audit = $assembler->toVisibilityAuditDto([
            new VisibilityAuditItem(
                'promo',
                'Promo',
                ['Catalog', 'Promo'],
                ['promo_without_searchable']
            ),
        ]);

        self::assertInstanceOf(VisibilityAuditDto::class, $audit);
        self::assertCount(1, $audit->items);
        self::assertSame('promo', $audit->items[0]->id);
        self::assertSame('Promo', $audit->items[0]->name);
        self::assertSame(['Catalog', 'Promo'], $audit->items[0]->path);
        self::assertSame(['promo_without_searchable'], $audit->items[0]->reasons);
    }
}
