<?php

declare(strict_types=1);

namespace FunTask\Tests\Bridge\Container;

use FunTask\Bridge\Container\ContainerFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ContainerFactoryTest extends TestCase
{
    public function testCreateReturnsContainerWithConsoleApplication(): void
    {
        $container = ContainerFactory::create();

        self::assertInstanceOf(ContainerBuilder::class, $container);
        self::assertTrue($container->has(Application::class));
        self::assertInstanceOf(Application::class, $container->get(Application::class));
    }
}
