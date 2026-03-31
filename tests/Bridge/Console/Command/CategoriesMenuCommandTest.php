<?php

declare(strict_types=1);

namespace FunTask\Tests\Bridge\Console\Command;

use FunTask\Application\Service\BuildMenuService;
use FunTask\Application\Dto\MenuAssembler;
use FunTask\Bridge\Console\Command\CategoriesMenuCommand;
use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Tag;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoriesMenuCommandTest extends TestCase
{
    public function testExecuteUsesDefaultBehaviorWhenOptionalOptionsAreOmitted(): void
    {
        $command = new CategoriesMenuCommand(
            new BuildMenuService(
                $this->createHydrator(
                    $this->createCategory(
                        'root',
                        'Catalog',
                        ['root'],
                        [
                            $this->createCategory('visible-kg', 'Visible KG', ['menu', 'region:kg']),
                            $this->createCategory('visible-ru', 'Visible RU', ['menu', 'region:ru']),
                            $this->createCategory('adult', 'Adult', ['menu', 'restricted:18plus']),
                            $this->createCategory('staff', 'Staff', ['menu', 'restricted:staff-only']),
                        ]
                    )
                ),
                new MenuAssembler()
            )
        );
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'path' => 'data/categories.json',
        ]);
        self::assertSame(0, $exitCode);
        self::assertSame("- Visible KG\n- Visible RU\n", $commandTester->getDisplay());
    }
    public function testExecutePrintsMenuTree(): void
    {
        $command = new CategoriesMenuCommand(
            new BuildMenuService(
                $this->createHydrator(
                    $this->createCategory(
                        'root',
                        'Catalog',
                        ['root'],
                        [
                            $this->createCategory(
                                'electronics',
                                'Electronics',
                                ['menu'],
                                [
                                    $this->createCategory('smartphones', 'Smartphones', ['menu']),
                                ]
                            ),
                        ]
                    )
                ),
                new MenuAssembler()
            )
        );
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'path' => 'data/categories.json',
            '--adult' => 'false',
            '--region' => 'kg',
            '--staff' => 'false',
        ]);
        self::assertSame(0, $exitCode);
        self::assertSame("- Electronics\n  - Smartphones\n", $commandTester->getDisplay());
    }
    public function testExecuteRejectsUnsupportedRegionOption(): void
    {
        $command = new CategoriesMenuCommand(
            new BuildMenuService(
                $this->createHydrator($this->createCategory('root', 'Catalog', ['root'])),
                new MenuAssembler()
            )
        );
        $commandTester = new CommandTester($command);
        $this->expectException(InvalidOptionException::class);
        $commandTester->execute([
            'path' => 'data/categories.json',
            '--adult' => 'false',
            '--region' => 'eu',
            '--staff' => 'false',
        ]);
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
