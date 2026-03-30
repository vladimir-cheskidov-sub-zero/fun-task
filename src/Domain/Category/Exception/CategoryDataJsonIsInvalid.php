<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class CategoryDataJsonIsInvalid extends CategoryHydrationFailed
{
    public static function becauseJsonCannotBeDecoded(string $path, string $error): self
    {
        return new self(sprintf('Category data file "%s" contains invalid JSON: %s', $path, $error));
    }
}
