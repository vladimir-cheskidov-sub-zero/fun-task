<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class DuplicateCategoryTreeId extends DomainRuleViolation
{
    public static function becauseIdIsRepeated(string $categoryId): self
    {
        return new self(sprintf('Category tree contains duplicate id "%s".', $categoryId));
    }
}
