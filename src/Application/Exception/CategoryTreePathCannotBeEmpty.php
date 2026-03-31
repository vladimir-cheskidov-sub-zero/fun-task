<?php

declare(strict_types=1);

namespace FunTask\Application\Exception;

final class CategoryTreePathCannotBeEmpty extends \InvalidArgumentException
{
    public static function becauseValueIsEmpty(): self
    {
        return new self('Category tree path cannot be empty.');
    }
}
