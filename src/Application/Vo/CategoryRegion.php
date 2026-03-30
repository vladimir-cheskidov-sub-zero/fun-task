<?php

declare(strict_types=1);

namespace FunTask\Application\Vo;

use FunTask\Domain\Category\Region;
use MyCLabs\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class CategoryRegion extends Enum
{
    private const UNSPECIFIED = 'unspecified';
    private const KG = 'kg';
    private const RU = 'ru';

    /**
     * @throws \UnexpectedValueException
     */
    protected function __construct(string $value)
    {
        parent::__construct($value);
    }

    public static function UNSPECIFIED(): self
    {
        return new self(self::UNSPECIFIED);
    }

    public static function KG(): self
    {
        return new self(self::KG);
    }

    public static function RU(): self
    {
        return new self(self::RU);
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

    public function toDomainRegion(): Region
    {
        return Region::from($this->getValue());
    }
}
