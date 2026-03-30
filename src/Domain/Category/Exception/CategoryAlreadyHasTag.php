<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class CategoryAlreadyHasTag extends DomainRuleViolation
{
    public static function becauseTagIsAlreadyAssigned(string $tag): self
    {
        return new self(sprintf('Category already has tag "%s".', $tag));
    }
}
