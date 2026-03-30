<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class CategoryCannotContainItself extends DomainRuleViolation
{
    public static function becauseIdsMatch(string $categoryId): self
    {
        return new self(sprintf('Category "%s" cannot contain itself.', $categoryId));
    }
}
