<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class CategoryNameIsAlreadyUsed extends DomainRuleViolation
{
    public static function becauseRenameKeepsSameValue(string $name): self
    {
        return new self(sprintf('Category already has name "%s".', $name));
    }
}
