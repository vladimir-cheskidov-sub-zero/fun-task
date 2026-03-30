<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use MyCLabs\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class Region extends Enum
{
    public const UNSPECIFIED = 'unspecified';
    public const KG = 'kg';
    public const RU = 'ru';
}
