<?php

declare(strict_types=1);

namespace FunTask\Application\Category;

use FunTask\Application\Dto\Menu;
use FunTask\Application\Dto\MenuAssembler;
use FunTask\Application\Exception\BuildMenuFailed;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Domain\Category\Exception\DomainRuleViolation;
use FunTask\Domain\Category\Visitor\MenuBuilderVisitor;

final class BuildMenuService
{
    private CategoryHydrator $categoryHydrator;
    private MenuAssembler $menuAssembler;
    public function __construct(CategoryHydrator $categoryHydrator, MenuAssembler $menuAssembler)
    {
        $this->categoryHydrator = $categoryHydrator;
        $this->menuAssembler = $menuAssembler;
    }
    /**
     * @throws BuildMenuFailed
     */
    public function execute(BuildMenu $query): Menu
    {
        $visitor = new MenuBuilderVisitor(
            $query->adultEnabled(),
            $query->staffEnabled(),
            $query->region()->toDomainRegion()
        );
        try {
            $categoryTree = $this->categoryHydrator->hydrate($query->path());
            $categoryTree->accept($visitor);
        } catch (DomainRuleViolation $exception) {
            throw BuildMenuFailed::becauseDomainRuleWasViolated($query->path(), $exception);
        }
        return $this->menuAssembler->assemble($visitor->menuItems());
    }
}
