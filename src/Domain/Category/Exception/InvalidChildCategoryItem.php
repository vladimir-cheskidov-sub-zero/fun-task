<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class InvalidChildCategoryItem extends DomainRuleViolation
{
    public static function becauseItemHasInvalidType(): self
    {
        return new self('Child category collection accepts only Category instances.');
    }
}
