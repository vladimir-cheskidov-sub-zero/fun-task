<?php
declare(strict_types=1);
namespace FunTask\Domain\Category\Exception;
final class UnknownRestrictedTagValue extends DomainRuleViolation
{
    public static function becauseValueIsUnsupported(string $value, \Throwable $previous = null): self
    {
        return new self(sprintf('Unknown restricted tag value "%s".', $value), 0, $previous);
    }
}
