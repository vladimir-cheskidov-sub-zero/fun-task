<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class VisibilityAuditItemHasNoReasons extends DomainRuleViolation
{
    public static function becauseReasonsAreMissing(): self
    {
        return new self('Visibility audit item must contain at least one reason.');
    }
}
