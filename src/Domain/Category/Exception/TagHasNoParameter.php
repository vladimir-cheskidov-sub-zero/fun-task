<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class TagHasNoParameter extends DomainRuleViolation
{
    public static function becauseTagIsSimple(string $value): self
    {
        return new self(sprintf('Tag "%s" does not have a parameter.', $value));
    }
}
