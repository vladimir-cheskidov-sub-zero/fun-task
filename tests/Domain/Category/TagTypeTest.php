<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category;

use FunTask\Domain\Category\TagType;
use PHPUnit\Framework\TestCase;

final class TagTypeTest extends TestCase
{
    public function testFactoriesReturnExpectedValues(): void
    {
        self::assertSame('root', TagType::ROOT()->getValue());
        self::assertSame('menu', TagType::MENU()->getValue());
        self::assertSame('promo', TagType::PROMO()->getValue());
        self::assertSame('hidden', TagType::HIDDEN()->getValue());
        self::assertSame('searchable', TagType::SEARCHABLE()->getValue());
        self::assertSame('restricted', TagType::RESTRICTED()->getValue());
        self::assertSame('region', TagType::REGION()->getValue());
    }

    public function testEqualsAnyMatchesAnyProvidedType(): void
    {
        self::assertTrue(TagType::REGION()->equalsAny(TagType::MENU(), TagType::REGION()));
    }

    public function testConstructorIsNotPublic(): void
    {
        self::assertFalse((new \ReflectionMethod(TagType::class, '__construct'))->isPublic());
    }
}
