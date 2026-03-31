<?php

declare(strict_types=1);

namespace FunTask\Application\Exception;

use FunTask\Domain\Category\Exception\DomainRuleViolation;

final class SearchIndexExportFailed extends \RuntimeException
{
    public static function becauseDomainRuleWasViolated(string $path, DomainRuleViolation $previous): self
    {
        return new self(
            sprintf('Failed to export search index from "%s": %s', $path, $previous->getMessage()),
            0,
            $previous
        );
    }
}
