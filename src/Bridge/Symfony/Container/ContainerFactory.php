<?php

declare(strict_types=1);

namespace FunTask\Bridge\Symfony\Container;

use FunTask\Application\Dto\MenuAssembler;
use FunTask\Application\Service\BuildMenuService;
use FunTask\Bridge\Symfony\Console\ApplicationFactory;
use FunTask\Bridge\Symfony\Console\Command\CategoriesMenuCommand;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Infrastructure\Category\JsonFileCategoryHydrator;
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
            ->register(JsonFileCategoryHydrator::class, JsonFileCategoryHydrator::class)
            ->setPublic(false);
        $container
            ->setAlias(CategoryHydrator::class, JsonFileCategoryHydrator::class)
            ->setPublic(false);
        $container
            ->register(MenuAssembler::class, MenuAssembler::class)
            ->setPublic(false);
        $container
            ->register(BuildMenuService::class, BuildMenuService::class)
            ->setArgument('$categoryHydrator', new Reference(CategoryHydrator::class))
            ->setArgument('$menuAssembler', new Reference(MenuAssembler::class))
            ->setPublic(false);
        $container
            ->register(CategoriesMenuCommand::class, CategoriesMenuCommand::class)
            ->setArgument('$buildMenuUseCase', new Reference(BuildMenuService::class))
            ->setPublic(false);
        $container
            ->register(Application::class, Application::class)
            ->setFactory([new Reference(ApplicationFactory::class), 'create'])
            ->addMethodCall('add', [new Reference(CategoriesMenuCommand::class)])
            ->setPublic(true);
        $container->compile();
        return $container;
    }
}
