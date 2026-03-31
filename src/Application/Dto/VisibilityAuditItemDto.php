<?php

declare(strict_types=1);

namespace FunTask\Application\Dto;

final class VisibilityAuditItemDto
{
    public string $id;
    public string $name;
    /**
     * @var string[]
     */
    public array $path = [];
    /**
     * @var string[]
     */
    public array $reasons = [];
}
