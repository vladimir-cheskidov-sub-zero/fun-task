<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category\Visitor;

use FunTask\Domain\Category\Region;
use FunTask\Domain\Category\Visitor\SearchIndexDocument;
use PHPUnit\Framework\TestCase;

final class SearchIndexDocumentTest extends TestCase
{
    public function testAccessorsReturnAssignedValues(): void
    {
        $document = new SearchIndexDocument(
            'wine',
            'Wine',
            true,
            [Region::RU(), Region::KG()]
        );
        self::assertSame('wine', $document->id());
        self::assertSame('Wine', $document->name());
        self::assertTrue($document->adult());
        self::assertSame(['ru', 'kg'], array_map(static function (Region $region): string {
            return $region->getValue();
        }, $document->regions()));
    }
}
