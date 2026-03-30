<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class TagIsNotRegional extends DomainRuleViolation
{
    public static function becauseTypeDiffers(string $value): self
    {
        return new self(sprintf('Tag "%s" is not a region tag.', $value));
    }
}
