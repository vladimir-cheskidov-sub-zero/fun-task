<?php

declare(strict_types=1);

namespace FunTask\Application\Service;

use FunTask\Application\Dto\SearchIndexExportAssembler;
use FunTask\Application\Dto\SearchIndexExportDto;
use FunTask\Application\Exception\SearchIndexExportFailed;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Domain\Category\Exception\DomainRuleViolation;
use FunTask\Domain\Category\Visitor\SearchIndexExportVisitor;

final class SearchIndexExportService
{
    private CategoryHydrator $categoryHydrator;
    private SearchIndexExportAssembler $searchIndexExportAssembler;
    public function __construct(
        CategoryHydrator $categoryHydrator,
        SearchIndexExportAssembler $searchIndexExportAssembler
    ) {
        $this->categoryHydrator = $categoryHydrator;
        $this->searchIndexExportAssembler = $searchIndexExportAssembler;
    }
    /**
     * @throws SearchIndexExportFailed
     */
    public function execute(SearchIndexExport $query): SearchIndexExportDto
    {
        $visitor = new SearchIndexExportVisitor();
        try {
            $categoryTree = $this->categoryHydrator->hydrate($query->path());
            $categoryTree->accept($visitor);
        } catch (DomainRuleViolation $exception) {
            throw SearchIndexExportFailed::becauseDomainRuleWasViolated($query->path(), $exception);
        }
        return $this->searchIndexExportAssembler->toSearchIndexExportDto($visitor->documents());
    }
}
