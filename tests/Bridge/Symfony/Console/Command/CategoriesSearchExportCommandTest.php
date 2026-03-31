<?php

declare(strict_types=1);

namespace FunTask\Tests\Bridge\Console\Command;

use FunTask\Application\Dto\SearchIndexExportAssembler;
use FunTask\Application\Service\SearchIndexExportService;
use FunTask\Bridge\Symfony\Console\Command\CategoriesSearchExportCommand;
use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Tag;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoriesSearchExportCommandTest extends TestCase
{
    public function testExecuteWritesJsonExportFileAndPrintsPath(): void
    {
        $command = new CategoriesSearchExportCommand(
            new SearchIndexExportService(
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
            )
        );
        $commandTester = new CommandTester($command);
        $workingDirectory = $this->createTemporaryDirectory();
        $originalWorkingDirectory = getcwd();
        if (false === $originalWorkingDirectory) {
            self::fail('Unable to determine current working directory.');
        }
        chdir($workingDirectory);
        try {
            $exitCode = $commandTester->execute([
                'path' => 'data/categories.json',
            ]);
        } finally {
            chdir($originalWorkingDirectory);
        }
        $exportFiles = glob($workingDirectory . '/export/*-index.json');
        if (false === $exportFiles) {
            $this->removeDirectory($workingDirectory);
            self::fail('Unable to list generated export files.');
        }
        try {
            self::assertSame(0, $exitCode);
            self::assertCount(1, $exportFiles);
            self::assertMatchesRegularExpression('/^export\/\d{14}-index\.json\n$/', $commandTester->getDisplay());
            $exportContents = file_get_contents($exportFiles[0]);
            if (false === $exportContents) {
                self::fail('Unable to read generated export file.');
            }
            self::assertStringContainsString('"id": "electronics"', $exportContents);
            self::assertSame(
                [
                    [
                        'id' => 'electronics',
                        'name' => 'Electronics',
                        'adult' => false,
                        'regions' => [],
                    ],
                    [
                        'id' => 'wine',
                        'name' => 'Wine',
                        'adult' => true,
                        'regions' => ['ru'],
                    ],
                ],
                json_decode($exportContents, true)
            );
        } finally {
            $this->removeDirectory($workingDirectory);
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
    private function createTemporaryDirectory(): string
    {
        $directory = sprintf('%s/%s', sys_get_temp_dir(), uniqid('categories-search-export-', true));
        mkdir($directory, 0777, true);
        return $directory;
    }
    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }
        $items = scandir($directory);
        if (false === $items) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $directory . '/' . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
                continue;
            }
            unlink($path);
        }
        rmdir($directory);
    }
}
