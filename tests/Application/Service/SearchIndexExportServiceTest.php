<?php

declare(strict_types=1);

namespace FunTask\Tests\Application\Service;

use FunTask\Application\Dto\SearchIndexExportAssembler;
use FunTask\Application\Exception\SearchIndexExportFailed;
use FunTask\Application\Service\SearchIndexExport;
use FunTask\Application\Service\SearchIndexExportService;
use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Exception\CategoryHydrationFailed;
use FunTask\Domain\Category\Tag;
use PHPUnit\Framework\TestCase;

final class SearchIndexExportServiceTest extends TestCase
{
    public function testExecuteReturnsSearchIndexExportDto(): void
    {
        $useCase = new SearchIndexExportService(
            $this->createHydrator(
                $this->createCategory(
                    'root',
                    'Catalog',
                    ['root'],
                    [
                        $this->createCategory('electronics', 'Electronics', ['searchable']),
                        $this->createCategory('wine', 'Wine', ['searchable', 'restricted:18plus', 'region:ru']),
                        $this->createCategory('staff', 'Staff', ['searchable', 'restricted:staff-only']),
                    ]
                )
            ),
            new SearchIndexExportAssembler()
        );
        $export = $useCase->execute(new SearchIndexExport('data/categories.json'));
        self::assertCount(2, $export->documents);
        self::assertSame('electronics', $export->documents[0]->id);
        self::assertFalse($export->documents[0]->adult);
        self::assertSame([], $export->documents[0]->regions);
        self::assertSame('wine', $export->documents[1]->id);
        self::assertTrue($export->documents[1]->adult);
        self::assertSame(['ru'], $export->documents[1]->regions);
    }
    public function testExecuteMapsDomainExceptionsToApplicationException(): void
    {
        $expectedException = new class ('Broken tree.') extends CategoryHydrationFailed {
        };
        $useCase = new SearchIndexExportService(
            new class ($expectedException) implements CategoryHydrator {
                private CategoryHydrationFailed $exception;
                public function __construct(CategoryHydrationFailed $exception)
                {
                    $this->exception = $exception;
                }
                public function hydrate(string $path): Category
                {
                    throw $this->exception;
                }
            },
            new SearchIndexExportAssembler()
        );
        try {
            $useCase->execute(new SearchIndexExport('missing.json'));
            self::fail('Expected application exception was not thrown.');
        } catch (SearchIndexExportFailed $exception) {
            self::assertSame($expectedException, $exception->getPrevious());
            self::assertSame(
                'Failed to export search index from "missing.json": Broken tree.',
                $exception->getMessage()
            );
        }
    }
    private function createHydrator(Category $category): CategoryHydrator
    {
        return new class ($category) implements CategoryHydrator {
            private Category $category;
            public function __construct(Category $category)
            {
                $this->category = $category;
            }
            public function hydrate(string $path): Category
            {
                return $this->category;
            }
        };
    }
    /**
     * @param string[] $tags
     * @param Category[] $children
     */
    private function createCategory(string $id, string $name, array $tags, array $children = []): Category
    {
        return new Category(
            new CategoryId($id),
            new CategoryName($name),
            new CategoryTags(array_map(static function (string $tag): Tag {
                return new Tag($tag);
            }, $tags)),
            new ChildCategories($children)
        );
    }
}
