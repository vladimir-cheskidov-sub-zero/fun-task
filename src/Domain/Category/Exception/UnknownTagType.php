<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class UnknownTagType extends DomainRuleViolation
{
    public static function becauseTypeIsUnsupported(string $type, \Throwable $previous = null): self
    {
        return new self(sprintf('Unknown tag type "%s".', $type), 0, $previous);
    }
}
