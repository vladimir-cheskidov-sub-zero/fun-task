<?php

declare(strict_types=1);

namespace FunTask\Infrastructure\Category\Exception;

use FunTask\Domain\Category\Exception\CategoryHydrationFailed;

final class CategoryDataFileWasNotFound extends CategoryHydrationFailed
{
    public static function becausePathDoesNotExist(string $path): self
    {
        return new self(sprintf('Category data file "%s" was not found.', $path));
    }
}
