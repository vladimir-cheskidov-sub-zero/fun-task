<?php

declare(strict_types=1);

namespace FunTask\Tests\Bridge\Console\Command;

use FunTask\Application\Dto\VisibilityAuditAssembler;
use FunTask\Application\Service\AuditVisibilityService;
use FunTask\Bridge\Symfony\Console\Command\CategoriesAuditCommand;
use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Tag;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategoriesAuditCommandTest extends TestCase
{
    public function testExecutePrintsOneLinePerProblematicCategory(): void
    {
        $command = new CategoriesAuditCommand(
            new AuditVisibilityService(
                $this->createHydrator(
                    $this->createCategory(
                        'root',
                        'Catalog',
                        ['root'],
                        [
                            $this->createCategory('hidden', 'Hidden', ['hidden']),
                            $this->createCategory('promo', 'Promo', ['promo']),
                            $this->createCategory('ok', 'Ok', ['searchable']),
                        ]
                    )
                ),
                new VisibilityAuditAssembler()
            )
        );

        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'path' => 'data/categories.json',
        ]);

        self::assertSame(0, $exitCode);
        self::assertSame(
            "Catalog > Hidden [hidden]: hidden\nCatalog > Promo [promo]: promo_without_searchable\n",
            $commandTester->getDisplay()
        );
    }

    public function testExecuteSanitizesControlCharactersInRenderedOutput(): void
    {
        $command = new CategoriesAuditCommand(
            new AuditVisibilityService(
                $this->createHydrator(
                    $this->createCategory(
                        'root',
                        'Catalog',
                        ['root'],
                        [
                            $this->createCategory("bad\x1Bid", "Bad\nName", ['hidden']),
                        ]
                    )
                ),
                new VisibilityAuditAssembler()
            )
        );

        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([
            'path' => 'data/categories.json',
        ]);

        self::assertSame(0, $exitCode);
        self::assertSame(
            "Catalog > Bad\\x0AName [bad\\x1Bid]: hidden\n",
            $commandTester->getDisplay()
        );
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
