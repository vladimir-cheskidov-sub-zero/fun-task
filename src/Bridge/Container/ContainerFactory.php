<?php

declare(strict_types=1);

namespace FunTask\Bridge\Container;

use FunTask\Bridge\Console\ApplicationFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ExceptionInterface;
use Symfony\Component\DependencyInjection\Reference;

final class ContainerFactory
{
    /**
     * @throws ExceptionInterface
     */
    public static function create(): ContainerBuilder
    {
        $container = new ContainerBuilder();

        $container
            ->register(ApplicationFactory::class, ApplicationFactory::class)
            ->setPublic(false);

        $container
            ->register(Application::class, Application::class)
            ->setFactory([new Reference(ApplicationFactory::class), 'create'])
            ->setPublic(true);

        $container->compile();

        return $container;
    }
}
