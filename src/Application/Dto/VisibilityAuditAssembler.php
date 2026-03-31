<?php

declare(strict_types=1);

namespace FunTask\Application\Dto;

use FunTask\Domain\Category\Visitor\VisibilityAuditItem;

final class VisibilityAuditAssembler
{
    /**
     * @param VisibilityAuditItem[] $auditItems
     */
    public function toVisibilityAuditDto(array $auditItems): VisibilityAuditDto
    {
        $audit = new VisibilityAuditDto();
        $audit->items = $this->toItemDtos($auditItems);

        return $audit;
    }

    /**
     * @param VisibilityAuditItem[] $auditItems
     *
     * @return VisibilityAuditItemDto[]
     */
    private function toItemDtos(array $auditItems): array
    {
        $items = [];

        foreach ($auditItems as $auditItem) {
            $item = new VisibilityAuditItemDto();
            $item->id = $auditItem->id();
            $item->name = $auditItem->name();
            $item->path = $auditItem->path();
            $item->reasons = $auditItem->reasons();
            $items[] = $item;
        }

        return $items;
    }
}
