<?php

declare(strict_types=1);

namespace FunTask\Tests\Domain\Category;

use FunTask\Domain\Category\Exception\TagHasNoParameter;
use FunTask\Domain\Category\Exception\TagIsNotRegional;
use FunTask\Domain\Category\Exception\TagParameterIsMissing;
use FunTask\Domain\Category\Exception\UnknownTagType;
use FunTask\Domain\Category\Region;
use FunTask\Domain\Category\RestrictedVisibility;
use FunTask\Domain\Category\Tag;
use FunTask\Domain\Category\TagType;
use PHPUnit\Framework\TestCase;

final class TagTest extends TestCase
{
    public function testConstructorParsesParameterizedTag(): void
    {
        $tag = new Tag('restricted:18plus');

        self::assertTrue($tag->isOfType(new TagType(TagType::RESTRICTED)));
        self::assertTrue($tag->hasParameter());
        self::assertTrue($tag->restrictedVisibility()->equals(new RestrictedVisibility(RestrictedVisibility::ADULTS_ONLY)));
    }

    public function testConstructorRejectsUnknownType(): void
    {
        $this->expectException(UnknownTagType::class);

        new Tag('legacy');
    }

    public function testConstructorRejectsMissingParameter(): void
    {
        $this->expectException(TagParameterIsMissing::class);

        new Tag('region');
    }

    public function testParameterThrowsForSimpleTag(): void
    {
        $this->expectException(TagHasNoParameter::class);

        (new Tag('menu'))->parameter();
    }

    public function testRegionThrowsForNonRegionalTag(): void
    {
        $this->expectException(TagIsNotRegional::class);

        (new Tag('promo'))->region();
    }

    public function testRegionReturnsEnumForRegionalTag(): void
    {
        self::assertTrue((new Tag('region:kg'))->region()->equals(new Region(Region::KG)));
    }
}
