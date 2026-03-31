<?php

declare(strict_types=1);

namespace FunTask\Tests\Application\Service;

use FunTask\Application\Exception\CategoryTreePathCannotBeEmpty;
use FunTask\Application\Service\SearchIndexExport;
use PHPUnit\Framework\TestCase;

final class SearchIndexExportTest extends TestCase
{
    public function testConstructorNormalizesPath(): void
    {
        $query = new SearchIndexExport(' data/categories.json ');
        self::assertSame('data/categories.json', $query->path());
    }
    public function testConstructorRejectsEmptyPath(): void
    {
        $this->expectException(CategoryTreePathCannotBeEmpty::class);
        new SearchIndexExport('   ');
    }
}
