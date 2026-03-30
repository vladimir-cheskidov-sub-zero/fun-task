<?php

declare(strict_types=1);

namespace FunTask\Infrastructure\Category\Exception;

use FunTask\Domain\Category\Exception\CategoryHydrationFailed;

final class CategoryDataFileIsNotReadable extends CategoryHydrationFailed
{
    public static function becausePathCannotBeRead(string $path): self
    {
        return new self(sprintf('Category data file "%s" is not readable.', $path));
    }
}
