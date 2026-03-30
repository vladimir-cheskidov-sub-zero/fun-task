<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Exception;

final class CategoryDataFileWasNotFound extends CategoryHydrationFailed
{
    public static function becausePathDoesNotExist(string $path): self
    {
        return new self(sprintf('Category data file "%s" was not found.', $path));
    }
}
