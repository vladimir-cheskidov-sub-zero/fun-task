<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class TagIsNotRestricted extends DomainRuleViolation
{
    public static function becauseTypeDiffers(string $value): self
    {
        return new self(sprintf('Tag "%s" is not a restricted tag.', $value));
    }
}
