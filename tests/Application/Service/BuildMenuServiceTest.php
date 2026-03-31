<?php

declare(strict_types=1);

namespace FunTask\tests\Application\Service;

use FunTask\Application\Service\BuildMenu;
use FunTask\Application\Service\BuildMenuService;
use FunTask\Application\Dto\MenuAssembler;
use FunTask\Application\Exception\BuildMenuFailed;
use FunTask\Application\Vo\CategoryRegion;
use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Exception\CategoryHydrationFailed;
use FunTask\Domain\Category\Tag;
use PHPUnit\Framework\TestCase;

final class BuildMenuServiceTest extends TestCase
{
    public function testExecuteReturnsMenuDto(): void
    {
        $useCase = new BuildMenuService(
            $this->createHydrator(
                $this->createCategory(
                    'root',
                    'Catalog',
                    ['root'],
                    [
                        $this->createCategory('electronics', 'Electronics', ['menu']),
                    ]
                )
            ),
            new MenuAssembler()
        );
        $menu = $useCase->execute(
            new BuildMenu('data/categories.json', false, CategoryRegion::KG(), false)
        );
        self::assertCount(1, $menu->items);
        self::assertSame('electronics', $menu->items[0]->id);
        self::assertSame('Electronics', $menu->items[0]->name);
    }
    public function testExecuteMapsDomainExceptionsToApplicationException(): void
    {
        $expectedException = new class ('Broken tree.') extends CategoryHydrationFailed {
        };
        $useCase = new BuildMenuService(
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
            new MenuAssembler()
        );
        try {
            $useCase->execute(
                new BuildMenu('missing.json', false, CategoryRegion::KG(), false)
            );
            self::fail('Expected application exception was not thrown.');
        } catch (BuildMenuFailed $exception) {
            self::assertSame($expectedException, $exception->getPrevious());
            self::assertSame('Failed to build menu from "missing.json": Broken tree.', $exception->getMessage());
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
