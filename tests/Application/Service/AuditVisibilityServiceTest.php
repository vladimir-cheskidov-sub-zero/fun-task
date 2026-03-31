<?php

declare(strict_types=1);

namespace FunTask\Tests\Application\Service;

use FunTask\Application\Dto\VisibilityAuditAssembler;
use FunTask\Application\Exception\AuditVisibilityFailed;
use FunTask\Application\Service\AuditVisibility;
use FunTask\Application\Service\AuditVisibilityService;
use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Exception\CategoryHydrationFailed;
use FunTask\Domain\Category\Tag;
use PHPUnit\Framework\TestCase;

final class AuditVisibilityServiceTest extends TestCase
{
    public function testExecuteReturnsAuditDto(): void
    {
        $useCase = new AuditVisibilityService(
            $this->createHydrator(
                $this->createCategory(
                    'root',
                    'Catalog',
                    ['root'],
                    [
                        $this->createCategory('hidden', 'Hidden', ['hidden']),
                    ]
                )
            ),
            new VisibilityAuditAssembler()
        );

        $audit = $useCase->execute(new AuditVisibility('data/categories.json'));

        self::assertCount(1, $audit->items);
        self::assertSame('hidden', $audit->items[0]->id);
        self::assertSame(['Catalog', 'Hidden'], $audit->items[0]->path);
        self::assertSame(['hidden'], $audit->items[0]->reasons);
    }

    public function testExecuteMapsDomainExceptionsToApplicationException(): void
    {
        $expectedException = new class ('Broken tree.') extends CategoryHydrationFailed {
        };

        $useCase = new AuditVisibilityService(
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
            new VisibilityAuditAssembler()
        );

        try {
            $useCase->execute(new AuditVisibility('missing.json'));
            self::fail('Expected application exception was not thrown.');
        } catch (AuditVisibilityFailed $exception) {
            self::assertSame($expectedException, $exception->getPrevious());
            self::assertSame(
                'Failed to audit visibility from "missing.json": Broken tree.',
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
