<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category;

use FunTask\Domain\Category\RestrictedVisibility;
use PHPUnit\Framework\TestCase;

final class RestrictedVisibilityTest extends TestCase
{
    public function testFactoriesReturnExpectedValues(): void
    {
        self::assertSame('staff-only', RestrictedVisibility::STAFF_ONLY()->getValue());
        self::assertSame('18plus', RestrictedVisibility::ADULTS_ONLY()->getValue());
    }

    public function testEqualsAnyMatchesAnyProvidedVisibility(): void
    {
        self::assertTrue(
            RestrictedVisibility::ADULTS_ONLY()->equalsAny(
                RestrictedVisibility::STAFF_ONLY(),
                RestrictedVisibility::ADULTS_ONLY()
            )
        );
    }

    public function testConstructorIsNotPublic(): void
    {
        self::assertFalse((new \ReflectionMethod(RestrictedVisibility::class, '__construct'))->isPublic());
    }
}
