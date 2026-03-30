<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use FunTask\Domain\Category\Exception\CategoryNameCannotBeEmpty;

final class CategoryName
{
    private string $value;

    /**
     * @throws CategoryNameCannotBeEmpty
     */
    public function __construct(string $value)
    {
        $normalizedValue = trim($value);
        if ($normalizedValue === '') {
            throw CategoryNameCannotBeEmpty::becauseValueIsEmpty();
        }

        $this->value = $normalizedValue;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
