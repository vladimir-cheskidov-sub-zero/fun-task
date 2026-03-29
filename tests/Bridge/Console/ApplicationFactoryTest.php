<?php

declare(strict_types=1);

namespace FunTask\Tests\Bridge\Console;

use FunTask\Bridge\Console\ApplicationFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

final class ApplicationFactoryTest extends TestCase
{
    public function testCreateReturnsConsoleApplication(): void
    {
        $application = ApplicationFactory::create();

        self::assertInstanceOf(Application::class, $application);
        self::assertSame('Categories', $application->getName());
        self::assertSame('0.1.0', $application->getVersion());
    }
}
