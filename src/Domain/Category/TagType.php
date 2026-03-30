<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use MyCLabs\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class TagType extends Enum
{
    public const ROOT = 'root';
    public const MENU = 'menu';
    public const PROMO = 'promo';
    public const HIDDEN = 'hidden';
    public const SEARCHABLE = 'searchable';
    public const RESTRICTED = 'restricted';
    public const REGION = 'region';

    public function requiresParameter(): bool
    {
        return $this->equals(new self(self::RESTRICTED)) || $this->equals(new self(self::REGION));
    }
}
