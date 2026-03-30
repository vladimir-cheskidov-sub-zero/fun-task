<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class UnexpectedTagParameter extends DomainRuleViolation
{
    public static function becauseTypeDoesNotAllowParameter(string $type): self
    {
        return new self(sprintf('Tag type "%s" must not have a parameter.', $type));
    }
}
