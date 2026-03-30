<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use MyCLabs\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class TagType extends Enum
{
    private const ROOT = 'root';
    private const MENU = 'menu';
    private const PROMO = 'promo';
    private const HIDDEN = 'hidden';
    private const SEARCHABLE = 'searchable';
    private const RESTRICTED = 'restricted';
    private const REGION = 'region';

    /**
     * @throws \UnexpectedValueException
     */
    protected function __construct(string $value)
    {
        parent::__construct($value);
    }

    public static function ROOT(): self
    {
        return new self(self::ROOT);
    }

    public static function MENU(): self
    {
        return new self(self::MENU);
    }

    public static function PROMO(): self
    {
        return new self(self::PROMO);
    }

    public static function HIDDEN(): self
    {
        return new self(self::HIDDEN);
    }

    public static function SEARCHABLE(): self
    {
        return new self(self::SEARCHABLE);
    }

    public static function RESTRICTED(): self
    {
        return new self(self::RESTRICTED);
    }

    public static function REGION(): self
    {
        return new self(self::REGION);
    }

    /**
     * @param self ...$elements
     */
    public function equalsAny(self ...$elements): bool
    {
        foreach ($elements as $element) {
            if ($this->equals($element)) {
                return true;
            }
        }

        return false;
    }

    public function requiresParameter(): bool
    {
        return $this->equalsAny(self::RESTRICTED(), self::REGION());
    }
}
