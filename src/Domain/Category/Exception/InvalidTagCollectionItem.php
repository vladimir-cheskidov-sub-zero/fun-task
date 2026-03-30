<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class InvalidTagCollectionItem extends DomainRuleViolation
{
    public static function becauseItemHasInvalidType(): self
    {
        return new self('Tag collection accepts only Tag instances.');
    }
}
