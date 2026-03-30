<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class CategoryDataStructureIsInvalid extends CategoryHydrationFailed
{
    public static function becauseRootNodeIsInvalid(string $path): self
    {
        return new self(sprintf('Category data file "%s" must contain a category object as root node.', $path));
    }

    public static function becauseFieldIsMissing(string $path, string $field): self
    {
        return new self(sprintf('Category data file "%s" is missing required field "%s".', $path, $field));
    }

    public static function becauseFieldHasInvalidType(string $path, string $field, string $expectedType): self
    {
        return new self(sprintf('Category data file "%s" field "%s" must be %s.', $path, $field, $expectedType));
    }

    public static function becauseDomainRuleWasViolated(string $path, string $message): self
    {
        return new self(sprintf('Category data file "%s" violates domain rules: %s', $path, $message));
    }
}
