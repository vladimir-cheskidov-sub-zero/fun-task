<?php

declare(strict_types=1);

namespace FunTask\Infrastructure\Category\Exception;

use FunTask\Domain\Category\Exception\CategoryHydrationFailed;
use Throwable;

final class CategoryDataJsonIsInvalid extends CategoryHydrationFailed
{
    public static function becauseJsonCannotBeDecoded(string $path, string $error, Throwable $previous): self
    {
        return new self(
            sprintf('Category data file "%s" contains invalid JSON: %s', $path, $error),
            0,
            $previous
        );
    }
}
