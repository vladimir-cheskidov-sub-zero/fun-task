<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use FunTask\Domain\Category\Exception\CategoryIdCannotBeEmpty;

final class CategoryId
{
    private string $value;

    /**
     * @throws CategoryIdCannotBeEmpty
     */
    public function __construct(string $value)
    {
        $normalizedValue = trim($value);
        if ($normalizedValue === '') {
            throw CategoryIdCannotBeEmpty::becauseValueIsEmpty();
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
