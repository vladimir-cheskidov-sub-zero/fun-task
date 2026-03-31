<?php

declare(strict_types=1);

namespace FunTask\Bridge\Symfony\Container;

use FunTask\Application\Dto\MenuAssembler;
use FunTask\Application\Dto\SearchIndexExportAssembler;
use FunTask\Application\Dto\VisibilityAuditAssembler;
use FunTask\Application\Service\AuditVisibilityService;
use FunTask\Application\Service\BuildMenuService;
use FunTask\Application\Service\SearchIndexExportService;
use FunTask\Bridge\Symfony\Console\ApplicationFactory;
use FunTask\Bridge\Symfony\Console\Command\CategoriesAuditCommand;
use FunTask\Bridge\Symfony\Console\Command\CategoriesMenuCommand;
use FunTask\Bridge\Symfony\Console\Command\CategoriesSearchExportCommand;
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
            ->register(VisibilityAuditAssembler::class, VisibilityAuditAssembler::class)
            ->setPublic(false);
        $container
            ->register(SearchIndexExportAssembler::class, SearchIndexExportAssembler::class)
            ->setPublic(false);
        $container
            ->register(BuildMenuService::class, BuildMenuService::class)
            ->setArgument('$categoryHydrator', new Reference(CategoryHydrator::class))
            ->setArgument('$menuAssembler', new Reference(MenuAssembler::class))
            ->setPublic(false);
        $container
            ->register(AuditVisibilityService::class, AuditVisibilityService::class)
            ->setArgument('$categoryHydrator', new Reference(CategoryHydrator::class))
            ->setArgument('$visibilityAuditAssembler', new Reference(VisibilityAuditAssembler::class))
            ->setPublic(false);
        $container
            ->register(SearchIndexExportService::class, SearchIndexExportService::class)
            ->setArgument('$categoryHydrator', new Reference(CategoryHydrator::class))
            ->setArgument('$searchIndexExportAssembler', new Reference(SearchIndexExportAssembler::class))
            ->setPublic(false);
        $container
            ->register(CategoriesMenuCommand::class, CategoriesMenuCommand::class)
            ->setArgument('$buildMenuUseCase', new Reference(BuildMenuService::class))
            ->setPublic(false);
        $container
            ->register(CategoriesAuditCommand::class, CategoriesAuditCommand::class)
            ->setArgument('$auditVisibilityUseCase', new Reference(AuditVisibilityService::class))
            ->setPublic(false);
        $container
            ->register(CategoriesSearchExportCommand::class, CategoriesSearchExportCommand::class)
            ->setArgument('$searchIndexExportUseCase', new Reference(SearchIndexExportService::class))
            ->setPublic(false);
        $container
            ->register(Application::class, Application::class)
            ->setFactory([new Reference(ApplicationFactory::class), 'create'])
            ->addMethodCall('add', [new Reference(CategoriesMenuCommand::class)])
            ->addMethodCall('add', [new Reference(CategoriesAuditCommand::class)])
            ->addMethodCall('add', [new Reference(CategoriesSearchExportCommand::class)])
            ->setPublic(true);
        $container->compile();
        return $container;
    }
}
