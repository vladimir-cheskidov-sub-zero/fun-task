<?php

declare(strict_types=1);

namespace FunTask\Tests\Infrastructure\Category;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\Exception\CategoryDataFileWasNotFound;
use FunTask\Domain\Category\Exception\CategoryDataJsonIsInvalid;
use FunTask\Domain\Category\Exception\CategoryDataStructureIsInvalid;
use FunTask\Infrastructure\Category\JsonFileCategoryHydrator;
use PHPUnit\Framework\TestCase;

final class JsonFileCategoryHydratorTest extends TestCase
{
    public function testHydrateBuildsCategoryTreeFromJsonFile(): void
    {
        $hydrator = new JsonFileCategoryHydrator(__DIR__ . '/../../../data/categories.json');

        $category = $hydrator->hydrate();

        self::assertInstanceOf(Category::class, $category);
        self::assertSame('root', $category->id()->toString());
        self::assertSame('Каталог', $category->name()->toString());
        self::assertCount(6, $category->children()->all());
        self::assertSame('electronics', $category->children()->all()[0]->id()->toString());
    }

    public function testHydrateRejectsMissingFile(): void
    {
        $this->expectException(CategoryDataFileWasNotFound::class);

        (new JsonFileCategoryHydrator(__DIR__ . '/fixtures/missing.json'))->hydrate();
    }

    public function testHydrateRejectsInvalidJson(): void
    {
        $this->expectException(CategoryDataJsonIsInvalid::class);

        (new JsonFileCategoryHydrator(__DIR__ . '/fixtures/invalid-json.json'))->hydrate();
    }

    public function testHydrateRejectsInvalidStructure(): void
    {
        $this->expectException(CategoryDataStructureIsInvalid::class);

        (new JsonFileCategoryHydrator(__DIR__ . '/fixtures/invalid-structure.json'))->hydrate();
    }

    public function testHydrateWrapsDomainViolationsIntoHydrationException(): void
    {
        $this->expectException(CategoryDataStructureIsInvalid::class);

        (new JsonFileCategoryHydrator(__DIR__ . '/fixtures/invalid-domain-value.json'))->hydrate();
    }
}
