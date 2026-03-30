<?php

declare(strict_types=1);

namespace FunTask\Application\Vo;

use FunTask\Domain\Category\Region;
use MyCLabs\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class BuildMenuRegion extends Enum
{
    public const UNSPECIFIED = 'unspecified';
    public const KG = 'kg';
    public const RU = 'ru';

    public function toDomainRegion(): Region
    {
        return new Region($this->getValue());
    }
}
