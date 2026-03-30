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
    /**
     * @throws CategoryHydrationFailed
     */
    public function hydrate(string $path): Category
    {
        $payload = $this->decodeFile($path);
        try {
            return $this->hydrateCategory($payload, $path);
        } catch (CategoryDataStructureIsInvalid $exception) {
            throw $exception;
        } catch (DomainRuleViolation $exception) {
            throw CategoryDataStructureIsInvalid::becauseDomainRuleWasViolated(
                $path,
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
    private function decodeFile(string $path): array
    {
        if (!file_exists($path)) {
            throw CategoryDataFileWasNotFound::becausePathDoesNotExist($path);
        }
        if (!is_readable($path)) {
            throw CategoryDataFileIsNotReadable::becausePathCannotBeRead($path);
        }
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw CategoryDataFileIsNotReadable::becausePathCannotBeRead($path);
        }
        try {
            $payload = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw CategoryDataJsonIsInvalid::becauseJsonCannotBeDecoded(
                $path,
                $exception->getMessage(),
                $exception
            );
        }
        if (!is_array($payload)) {
            throw CategoryDataStructureIsInvalid::becauseRootNodeIsInvalid($path);
        }
        return $payload;
    }
    /**
     * @param array<string, mixed> $payload
     *
     * @throws DomainRuleViolation
     */
    private function hydrateCategory(array $payload, string $path): Category
    {
        return new Category(
            new CategoryId($this->requireString($payload, 'id', $path)),
            new CategoryName($this->requireString($payload, 'name', $path)),
            $this->hydrateTags($this->requireList($payload, 'tags', $path), $path),
            $this->hydrateChildren($this->requireList($payload, 'children', $path), $path)
        );
    }
    /**
     * @param array<int, mixed> $tags
     *
     * @throws DomainRuleViolation
     */
    private function hydrateTags(array $tags, string $path): CategoryTags
    {
        $items = [];
        foreach ($tags as $index => $tagValue) {
            if (!is_string($tagValue)) {
                throw CategoryDataStructureIsInvalid::becauseFieldHasInvalidType(
                    $path,
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
    private function hydrateChildren(array $children, string $path): ChildCategories
    {
        $items = [];
        foreach ($children as $index => $childPayload) {
            if (!is_array($childPayload)) {
                throw CategoryDataStructureIsInvalid::becauseFieldHasInvalidType(
                    $path,
                    sprintf('children[%d]', $index),
                    'an object'
                );
            }
            /** @var array<string, mixed> $childPayload */
            $items[] = $this->hydrateCategory($childPayload, $path);
        }
        return new ChildCategories($items);
    }
    /**
     * @param array<string, mixed> $payload
     *
     * @throws CategoryDataStructureIsInvalid
     */
    private function requireString(array $payload, string $field, string $path): string
    {
        if (!array_key_exists($field, $payload)) {
            throw CategoryDataStructureIsInvalid::becauseFieldIsMissing($path, $field);
        }
        if (!is_string($payload[$field])) {
            throw CategoryDataStructureIsInvalid::becauseFieldHasInvalidType($path, $field, 'a string');
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
    private function requireList(array $payload, string $field, string $path): array
    {
        if (!array_key_exists($field, $payload)) {
            throw CategoryDataStructureIsInvalid::becauseFieldIsMissing($path, $field);
        }
        if (!is_array($payload[$field])) {
            throw CategoryDataStructureIsInvalid::becauseFieldHasInvalidType($path, $field, 'a list');
        }
        return array_values($payload[$field]);
    }
}
