<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class CategoryDoesNotHaveTag extends DomainRuleViolation
{
    public static function becauseTagIsMissing(string $tag): self
    {
        return new self(sprintf('Category does not have tag "%s".', $tag));
    }
}
