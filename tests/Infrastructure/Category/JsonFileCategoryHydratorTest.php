<?php

declare(strict_types=1);

namespace FunTask\Tests\Infrastructure\Category;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\Exception\DomainRuleViolation;
use FunTask\Infrastructure\Category\Exception\CategoryDataFileWasNotFound;
use FunTask\Infrastructure\Category\Exception\CategoryDataJsonIsInvalid;
use FunTask\Infrastructure\Category\Exception\CategoryDataStructureIsInvalid;
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
        try {
            (new JsonFileCategoryHydrator(__DIR__ . '/fixtures/invalid-json.json'))->hydrate();
            self::fail('Expected invalid JSON exception was not thrown.');
        } catch (CategoryDataJsonIsInvalid $exception) {
            self::assertInstanceOf(\JsonException::class, $exception->getPrevious());
        }
    }

    public function testHydrateRejectsInvalidStructure(): void
    {
        try {
            (new JsonFileCategoryHydrator(__DIR__ . '/fixtures/invalid-structure.json'))->hydrate();
            self::fail('Expected invalid structure exception was not thrown.');
        } catch (CategoryDataStructureIsInvalid $exception) {
            self::assertNull($exception->getPrevious());
            self::assertSame(
                'Category data file "' . __DIR__ . '/fixtures/invalid-structure.json" field "children[0]" must be an object.',
                $exception->getMessage()
            );
        }
    }

    public function testHydrateWrapsDomainViolationsIntoHydrationException(): void
    {
        try {
            (new JsonFileCategoryHydrator(__DIR__ . '/fixtures/invalid-domain-value.json'))->hydrate();
            self::fail('Expected invalid structure exception was not thrown.');
        } catch (CategoryDataStructureIsInvalid $exception) {
            self::assertInstanceOf(DomainRuleViolation::class, $exception->getPrevious());
        }
    }
}
