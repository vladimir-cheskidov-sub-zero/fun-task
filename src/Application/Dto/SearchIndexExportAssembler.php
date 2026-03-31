<?php

declare(strict_types=1);

namespace FunTask\Application\Dto;

use FunTask\Domain\Category\Region;
use FunTask\Domain\Category\Visitor\SearchIndexDocument;

final class SearchIndexExportAssembler
{
    /**
     * @param SearchIndexDocument[] $documents
     */
    public function toSearchIndexExportDto(array $documents): SearchIndexExportDto
    {
        $export = new SearchIndexExportDto();
        $export->documents = $this->toDocumentDtos($documents);
        return $export;
    }
    /**
     * @param SearchIndexDocument[] $documents
     *
     * @return SearchIndexDocumentDto[]
     */
    private function toDocumentDtos(array $documents): array
    {
        $documentDtos = [];
        foreach ($documents as $document) {
            $documentDto = new SearchIndexDocumentDto();
            $documentDto->adult = $document->adult();
            $documentDto->id = $document->id();
            $documentDto->name = $document->name();
            $documentDto->regions = array_map(static function (Region $region): string {
                return $region->getValue();
            }, $document->regions());
            $documentDtos[] = $documentDto;
        }
        return $documentDtos;
    }
}
