<?php

declare(strict_types=1);

namespace FunTask\Application\Dto;

final class SearchIndexDocumentDto
{
    public bool $adult;
    public string $id;
    public string $name;
    /**
     * @var string[]
     */
    public array $regions = [];
}
