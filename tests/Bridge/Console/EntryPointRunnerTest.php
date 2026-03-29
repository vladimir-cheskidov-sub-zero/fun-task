<?php

declare(strict_types=1);

namespace FunTask\Tests\Bridge\Console;

use FunTask\Bridge\Console\EntryPointRunner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

final class EntryPointRunnerTest extends TestCase
{
    public function testRunReturnsApplicationExitCode(): void
    {
        $application = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['run'])
            ->getMock();
        $application
            ->expects(self::once())
            ->method('run')
            ->with(null, null)
            ->willReturn(7);

        $runner = new EntryPointRunner(
            static function () use ($application): Application {
                return $application;
            },
            static function (string $message): void {
            },
            static function (): bool {
                return false;
            }
        );

        self::assertSame(7, $runner->run());
    }

    public function testRunHandlesBootstrapException(): void
    {
        $messages = [];

        $runner = new EntryPointRunner(
            static function (): Application {
                throw new \RuntimeException('Container is broken.');
            },
            static function (string $message) use (&$messages): void {
                $messages[] = $message;
            },
            static function (): bool {
                return false;
            }
        );

        self::assertSame(1, $runner->run());
        self::assertSame(['Application failed: Container is broken.'], $messages);
    }

    public function testRunHandlesApplicationException(): void
    {
        $messages = [];
        $application = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['run'])
            ->getMock();
        $application
            ->expects(self::once())
            ->method('run')
            ->with(null, null)
            ->willThrowException(new \RuntimeException('Command failed.'));

        $runner = new EntryPointRunner(
            static function () use ($application): Application {
                return $application;
            },
            static function (string $message) use (&$messages): void {
                $messages[] = $message;
            },
            static function (): bool {
                return false;
            }
        );

        self::assertSame(1, $runner->run());
        self::assertSame(['Application failed: Command failed.'], $messages);
    }

    public function testRunOutputsFullExceptionInformationInVerboseMode(): void
    {
        $messages = [];

        $runner = new EntryPointRunner(
            static function (): Application {
                throw new \RuntimeException('Verbose failure.');
            },
            static function (string $message) use (&$messages): void {
                $messages[] = $message;
            },
            static function (): bool {
                return true;
            }
        );

        self::assertSame(1, $runner->run());
        self::assertStringContainsString('Application failed:', $messages[0]);
        self::assertStringContainsString('RuntimeException: Verbose failure.', $messages[0]);
    }
}
