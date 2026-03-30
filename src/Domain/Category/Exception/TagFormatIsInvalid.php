<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class TagFormatIsInvalid extends DomainRuleViolation
{
    public static function becauseValueHasUnexpectedStructure(string $value): self
    {
        return new self(sprintf('Tag "%s" has invalid format.', $value));
    }
}
