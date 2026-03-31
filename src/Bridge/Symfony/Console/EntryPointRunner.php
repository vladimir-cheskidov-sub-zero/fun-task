<?php

declare(strict_types=1);

namespace FunTask\Bridge\Symfony\Console;

use Closure;
use FunTask\Bridge\Symfony\Container\ContainerFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Exception\ExceptionInterface;

final class EntryPointRunner
{
    /**
     * @var Closure
     */
    private $applicationLoader;

    /**
     * @var Closure
     */
    private $errorWriter;

    /**
     * @var Closure
     */
    private $verbosityResolver;

    public function __construct(Closure $applicationLoader, Closure $errorWriter, Closure $verbosityResolver)
    {
        $this->applicationLoader = $applicationLoader;
        $this->errorWriter = $errorWriter;
        $this->verbosityResolver = $verbosityResolver;
    }

    public function run(): int
    {
        try {
            $application = ($this->applicationLoader)();

            return $application->run();
        } catch (\Throwable $throwable) {
            ($this->errorWriter)($this->formatErrorMessage($throwable));

            return 1;
        }
    }

    public static function createDefault(): self
    {
        return new self(
            static function (): Application {
                return self::loadApplication();
            },
            static function (string $message): void {
                fwrite(STDERR, $message . PHP_EOL);
            },
            static function (): bool {
                return self::isVerbose();
            }
        );
    }

    private function formatErrorMessage(\Throwable $throwable): string
    {
        if (($this->verbosityResolver)()) {
            return sprintf("Application failed:\n%s", (string) $throwable);
        }

        return sprintf('Application failed: %s', $throwable->getMessage());
    }

    /**
     * @throws ExceptionInterface
     */
    private static function loadApplication(): Application
    {
        /** @var Application $application */
        $application = ContainerFactory::create()->get(Application::class);

        return $application;
    }

    private static function isVerbose(): bool
    {
        $arguments = isset($_SERVER['argv']) && is_array($_SERVER['argv']) ? $_SERVER['argv'] : [];

        foreach ($arguments as $argument) {
            if ('--verbose' === $argument || '-v' === $argument || '-vv' === $argument || '-vvv' === $argument) {
                return true;
            }
        }

        return false;
    }
}
