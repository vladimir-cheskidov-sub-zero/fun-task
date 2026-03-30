<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class CategoryNameCannotBeEmpty extends DomainRuleViolation
{
    public static function becauseValueIsEmpty(): self
    {
        return new self('Category name cannot be empty.');
    }
}
