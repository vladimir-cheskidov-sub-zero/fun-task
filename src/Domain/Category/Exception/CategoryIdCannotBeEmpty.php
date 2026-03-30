<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class CategoryIdCannotBeEmpty extends DomainRuleViolation
{
    public static function becauseValueIsEmpty(): self
    {
        return new self('Category id cannot be empty.');
    }
}
