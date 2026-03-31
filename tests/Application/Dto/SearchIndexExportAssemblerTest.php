<?php

declare(strict_types=1);

namespace FunTask\Tests\Application\Dto;

use FunTask\Application\Dto\SearchIndexExportAssembler;
use FunTask\Application\Dto\SearchIndexExportDto;
use FunTask\Domain\Category\Region;
use FunTask\Domain\Category\Visitor\SearchIndexDocument;
use PHPUnit\Framework\TestCase;

final class SearchIndexExportAssemblerTest extends TestCase
{
    public function testToSearchIndexExportDtoBuildsDtoCollection(): void
    {
        $assembler = new SearchIndexExportAssembler();
        $export = $assembler->toSearchIndexExportDto([
            new SearchIndexDocument('wine', 'Wine', true, [Region::RU()]),
            new SearchIndexDocument('phones', 'Phones', false, []),
        ]);
        self::assertInstanceOf(SearchIndexExportDto::class, $export);
        self::assertCount(2, $export->documents);
        self::assertSame('wine', $export->documents[0]->id);
        self::assertSame('Wine', $export->documents[0]->name);
        self::assertTrue($export->documents[0]->adult);
        self::assertSame(['ru'], $export->documents[0]->regions);
        self::assertSame('phones', $export->documents[1]->id);
        self::assertFalse($export->documents[1]->adult);
        self::assertSame([], $export->documents[1]->regions);
    }
}
