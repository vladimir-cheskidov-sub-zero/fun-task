<?php

declare(strict_types=1);

namespace FunTask\Domain\Category\Visitor;

use FunTask\Domain\Category\Exception\VisibilityAuditItemHasNoReasons;

final class VisibilityAuditItem
{
    private string $id;
    private string $name;
    /**
     * @var string[]
     */
    private array $path;
    /**
     * @var string[]
     */
    private array $reasons;

    /**
     * @param string[] $path
     * @param string[] $reasons
     *
     * @throws VisibilityAuditItemHasNoReasons
     */
    public function __construct(string $id, string $name, array $path, array $reasons)
    {
        if ($reasons === []) {
            throw VisibilityAuditItemHasNoReasons::becauseReasonsAreMissing();
        }

        $this->id = $id;
        $this->name = $name;
        $this->path = array_values($path);
        $this->reasons = array_values($reasons);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function path(): array
    {
        return $this->path;
    }

    /**
     * @return string[]
     */
    public function reasons(): array
    {
        return $this->reasons;
    }
}
