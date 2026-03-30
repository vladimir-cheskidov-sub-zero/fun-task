<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use MyCLabs\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class RestrictedVisibility extends Enum
{
    private const STAFF_ONLY = 'staff-only';
    private const ADULTS_ONLY = '18plus';

    /**
     * @throws \UnexpectedValueException
     */
    protected function __construct(string $value)
    {
        parent::__construct($value);
    }

    public static function STAFF_ONLY(): self
    {
        return new self(self::STAFF_ONLY);
    }

    public static function ADULTS_ONLY(): self
    {
        return new self(self::ADULTS_ONLY);
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
}
