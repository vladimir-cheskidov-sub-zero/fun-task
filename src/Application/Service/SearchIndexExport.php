<?php

declare(strict_types=1);

namespace FunTask\Application\Service;

use FunTask\Application\Exception\CategoryTreePathCannotBeEmpty;

final class SearchIndexExport
{
    private string $path;
    /**
     * @throws CategoryTreePathCannotBeEmpty
     */
    public function __construct(string $path)
    {
        $normalizedPath = trim($path);
        if ($normalizedPath === '') {
            throw CategoryTreePathCannotBeEmpty::becauseValueIsEmpty();
        }
        $this->path = $normalizedPath;
    }
    public function path(): string
    {
        return $this->path;
    }
}
