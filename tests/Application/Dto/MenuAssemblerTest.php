<?php

declare(strict_types=1);

namespace FunTask\Tests\Application\Dto;

use FunTask\Application\Dto\MenuAssembler;
use FunTask\Application\Dto\MenuDto;
use FunTask\Domain\Category\Visitor\BuiltMenuItem;
use PHPUnit\Framework\TestCase;

final class MenuAssemblerTest extends TestCase
{
    public function testAssembleBuildsDtoTreeWithPublicProperties(): void
    {
        $assembler = new MenuAssembler();
        $electronics = new BuiltMenuItem('electronics', 'Electronics');
        $electronics->addChild(new BuiltMenuItem('smartphones', 'Smartphones'));

        $menu = $assembler->toMenuDto([$electronics]);

        self::assertInstanceOf(MenuDto::class, $menu);
        self::assertCount(1, $menu->items);
        self::assertSame('electronics', $menu->items[0]->id);
        self::assertSame('Electronics', $menu->items[0]->name);
        self::assertCount(1, $menu->items[0]->children);
        self::assertSame('smartphones', $menu->items[0]->children[0]->id);
        self::assertSame('Smartphones', $menu->items[0]->children[0]->name);
    }
}
