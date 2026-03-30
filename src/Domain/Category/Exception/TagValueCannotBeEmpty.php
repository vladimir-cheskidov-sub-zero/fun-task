<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class TagValueCannotBeEmpty extends DomainRuleViolation
{
    public static function becauseValueIsEmpty(): self
    {
        return new self('Tag value cannot be empty.');
    }
}
