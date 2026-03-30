<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use FunTask\Domain\Category\Exception\CategoryHydrationFailed;

interface CategoryHydrator
{
    /**
     * @throws CategoryHydrationFailed
     */
    public function hydrate(string $path): Category;
}
