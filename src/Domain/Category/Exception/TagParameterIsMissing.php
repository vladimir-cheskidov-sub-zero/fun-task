<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class TagParameterIsMissing extends DomainRuleViolation
{
    public static function becauseTypeRequiresParameter(string $type): self
    {
        return new self(sprintf('Tag type "%s" requires a parameter.', $type));
    }
}
