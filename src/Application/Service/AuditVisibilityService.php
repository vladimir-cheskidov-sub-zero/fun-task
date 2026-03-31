<?php

declare(strict_types=1);

namespace FunTask\Application\Service;

use FunTask\Application\Dto\VisibilityAuditAssembler;
use FunTask\Application\Dto\VisibilityAuditDto;
use FunTask\Application\Exception\AuditVisibilityFailed;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Domain\Category\Exception\DomainRuleViolation;
use FunTask\Domain\Category\Visitor\VisibilityAuditVisitor;

final class AuditVisibilityService
{
    private CategoryHydrator $categoryHydrator;
    private VisibilityAuditAssembler $visibilityAuditAssembler;

    public function __construct(
        CategoryHydrator $categoryHydrator,
        VisibilityAuditAssembler $visibilityAuditAssembler
    ) {
        $this->categoryHydrator = $categoryHydrator;
        $this->visibilityAuditAssembler = $visibilityAuditAssembler;
    }

    /**
     * @throws AuditVisibilityFailed
     */
    public function execute(AuditVisibility $query): VisibilityAuditDto
    {
        $visitor = new VisibilityAuditVisitor();

        try {
            $categoryTree = $this->categoryHydrator->hydrate($query->path());
            $categoryTree->accept($visitor);
        } catch (DomainRuleViolation $exception) {
            throw AuditVisibilityFailed::becauseDomainRuleWasViolated($query->path(), $exception);
        }

        return $this->visibilityAuditAssembler->toVisibilityAuditDto($visitor->auditItems());
    }
}
