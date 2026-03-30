<?php

declare(strict_types=1);

namespace FunTask\Infrastructure\Category;

use FunTask\Domain\Category\Category;
use FunTask\Domain\Category\CategoryHydrator;
use FunTask\Domain\Category\CategoryId;
use FunTask\Domain\Category\CategoryName;
use FunTask\Domain\Category\CategoryTags;
use FunTask\Domain\Category\ChildCategories;
use FunTask\Domain\Category\Exception\CategoryHydrationFailed;
use FunTask\Domain\Category\Exception\DomainRuleViolation;
use FunTask\Domain\Category\Tag;
use FunTask\Infrastructure\Category\Exception\CategoryDataFileIsNotReadable;
use FunTask\Infrastructure\Category\Exception\CategoryDataFileWasNotFound;
use FunTask\Infrastructure\Category\Exception\CategoryDataJsonIsInvalid;
use FunTask\Infrastructure\Category\Exception\CategoryDataStructureIsInvalid;
use JsonException;

final class JsonFileCategoryHydrator implements CategoryHydrator
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @throws CategoryHydrationFailed
     */
    public function hydrate(): Category
    {
        $payload = $this->decodeFile();

        try {
            return $this->hydrateCategory($payload);
        } catch (CategoryDataStructureIsInvalid $exception) {
            throw $exception;
        } catch (DomainRuleViolation $exception) {
            throw CategoryDataStructureIsInvalid::becauseDomainRuleWasViolated(
                $this->filePath,
                $exception->getMessage(),
                $exception
            );
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws CategoryHydrationFailed
     */
    private function decodeFile(): array
    {
        if (!file_exists($this->filePath)) {
            throw CategoryDataFileWasNotFound::becausePathDoesNotExist($this->filePath);
        }

        if (!is_readable($this->filePath)) {
            throw CategoryDataFileIsNotReadable::becausePathCannotBeRead($this->filePath);
        }

        $contents = file_get_contents($this->filePath);
        if ($contents === false) {
            throw CategoryDataFileIsNotReadable::becausePathCannotBeRead($this->filePath);
        }

        try {
            $payload = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw CategoryDataJsonIsInvalid::becauseJsonCannotBeDecoded(
                $this->filePath,
                $exception->getMessage(),
                $exception
            );
        }

        if (!is_array($payload)) {
            throw CategoryDataStructureIsInvalid::becauseRootNodeIsInvalid($this->filePath);
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws DomainRuleViolation
     */
    private function hydrateCategory(array $payload): Category
    {
        return new Category(
            new CategoryId($this->requireString($payload, 'id')),
            new CategoryName($this->requireString($payload, 'name')),
            $this->hydrateTags($this->requireList($payload, 'tags')),
            $this->hydrateChildren($this->requireList($payload, 'children'))
        );
    }

    /**
     * @param array<int, mixed> $tags
     *
     * @throws DomainRuleViolation
     */
    private function hydrateTags(array $tags): CategoryTags
    {
        $items = [];

        foreach ($tags as $index => $tagValue) {
            if (!is_string($tagValue)) {
                throw CategoryDataStructureIsInvalid::becauseFieldHasInvalidType(
                    $this->filePath,
                    sprintf('tags[%d]', $index),
                    'a string'
                );
            }

            $items[] = new Tag($tagValue);
        }

        return new CategoryTags($items);
    }

    /**
     * @param array<int, mixed> $children
     *
     * @throws DomainRuleViolation
     */
    private function hydrateChildren(array $children): ChildCategories
    {
        $items = [];

        foreach ($children as $index => $childPayload) {
            if (!is_array($childPayload)) {
                throw CategoryDataStructureIsInvalid::becauseFieldHasInvalidType(
                    $this->filePath,
                    sprintf('children[%d]', $index),
                    'an object'
                );
            }

            /** @var array<string, mixed> $childPayload */
            $items[] = $this->hydrateCategory($childPayload);
        }

        return new ChildCategories($items);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws CategoryDataStructureIsInvalid
     */
    private function requireString(array $payload, string $field): string
    {
        if (!array_key_exists($field, $payload)) {
            throw CategoryDataStructureIsInvalid::becauseFieldIsMissing($this->filePath, $field);
        }

        if (!is_string($payload[$field])) {
            throw CategoryDataStructureIsInvalid::becauseFieldHasInvalidType($this->filePath, $field, 'a string');
        }

        return $payload[$field];
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<int, mixed>
     *
     * @throws CategoryDataStructureIsInvalid
     */
    private function requireList(array $payload, string $field): array
    {
        if (!array_key_exists($field, $payload)) {
            throw CategoryDataStructureIsInvalid::becauseFieldIsMissing($this->filePath, $field);
        }

        if (!is_array($payload[$field])) {
            throw CategoryDataStructureIsInvalid::becauseFieldHasInvalidType($this->filePath, $field, 'a list');
        }

        return array_values($payload[$field]);
    }
}
