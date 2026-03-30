<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class CategoryChildWasNotFound extends DomainRuleViolation
{
    public static function becauseChildIdIsUnknown(string $childId): self
    {
        return new self(sprintf('Category child "%s" was not found.', $childId));
    }
}
