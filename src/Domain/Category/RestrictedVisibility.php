<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use MyCLabs\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class RestrictedVisibility extends Enum
{
    public const STAFF_ONLY = 'staff-only';
    public const ADULTS_ONLY = '18plus';
}
