<?php

declare(strict_types=1);

namespace FunTask\Bridge\Symfony\Console;

use Symfony\Component\Console\Application;

final class ApplicationFactory
{
    public static function create(): Application
    {
        return new Application('Categories', '0.1.0');
    }
}
