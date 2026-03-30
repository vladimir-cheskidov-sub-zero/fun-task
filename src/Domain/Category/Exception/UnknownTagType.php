<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class UnknownTagType extends DomainRuleViolation
{
    public static function becauseTypeIsUnsupported(string $type): self
    {
        return new self(sprintf('Unknown tag type "%s".', $type));
    }
}
