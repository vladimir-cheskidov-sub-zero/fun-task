<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use FunTask\Domain\Category\Exception\CategoryAlreadyHasTag;
use FunTask\Domain\Category\Exception\CategoryCannotContainItself;
use FunTask\Domain\Category\Exception\CategoryChildWasNotFound;
use FunTask\Domain\Category\Exception\CategoryDoesNotHaveTag;
use FunTask\Domain\Category\Exception\CategoryNameIsAlreadyUsed;
use FunTask\Domain\Category\Exception\DuplicateCategoryTreeId;
use FunTask\Domain\Category\Exception\InvalidChildCategoryItem;
use FunTask\Domain\Category\Exception\InvalidTagCollectionItem;

final class Category
{
    private CategoryId $id;
    private CategoryName $name;
    private CategoryTags $tags;
    private ChildCategories $children;

    /**
     * @throws CategoryCannotContainItself
     * @throws DuplicateCategoryTreeId
     */
    public function __construct(CategoryId $id, CategoryName $name, CategoryTags $tags, ChildCategories $children)
    {
        $this->assertTreeIdsAreUnique($id, $children);

        $this->id = $id;
        $this->name = $name;
        $this->tags = $tags;
        $this->children = $children;
    }

    /**
     * @throws CategoryNameIsAlreadyUsed
     * @throws CategoryCannotContainItself
     * @throws DuplicateCategoryTreeId
     */
    public function rename(CategoryName $name): self
    {
        if ($this->name->equals($name)) {
            throw CategoryNameIsAlreadyUsed::becauseRenameKeepsSameValue($name->toString());
        }

        return new self($this->id, $name, $this->tags, $this->children);
    }

    /**
     * @throws CategoryAlreadyHasTag
     * @throws CategoryCannotContainItself
     * @throws DuplicateCategoryTreeId
     * @throws InvalidTagCollectionItem
     */
    public function addTag(Tag $tag): self
    {
        return new self($this->id, $this->name, $this->tags->add($tag), $this->children);
    }

    /**
     * @throws CategoryCannotContainItself
     * @throws CategoryDoesNotHaveTag
     * @throws DuplicateCategoryTreeId
     * @throws InvalidTagCollectionItem
     */
    public function removeTag(Tag $tag): self
    {
        return new self($this->id, $this->name, $this->tags->remove($tag), $this->children);
    }

    /**
     * @throws CategoryCannotContainItself
     * @throws DuplicateCategoryTreeId
     * @throws InvalidChildCategoryItem
     */
    public function addChild(Category $child): self
    {
        if ($this->id->equals($child->id())) {
            throw CategoryCannotContainItself::becauseIdsMatch($this->id->toString());
        }

        return new self($this->id, $this->name, $this->tags, $this->children->add($child));
    }

    /**
     * @throws CategoryCannotContainItself
     * @throws CategoryChildWasNotFound
     * @throws DuplicateCategoryTreeId
     * @throws InvalidChildCategoryItem
     */
    public function removeChild(CategoryId $childId): self
    {
        return new self($this->id, $this->name, $this->tags, $this->children->removeById($childId));
    }

    public function id(): CategoryId
    {
        return $this->id;
    }

    public function name(): CategoryName
    {
        return $this->name;
    }

    public function tags(): CategoryTags
    {
        return $this->tags;
    }

    public function children(): ChildCategories
    {
        return $this->children;
    }

    public function hasTag(Tag $tag): bool
    {
        return $this->tags->contains($tag);
    }

    /**
     * @param array<string, true> $seenIds
     *
     * @throws DuplicateCategoryTreeId
     */
    private function collectIds(Category $category, array &$seenIds): void
    {
        $categoryId = $category->id()->toString();
        if (array_key_exists($categoryId, $seenIds)) {
            throw DuplicateCategoryTreeId::becauseIdIsRepeated($categoryId);
        }

        $seenIds[$categoryId] = true;

        foreach ($category->children() as $child) {
            $this->collectIds($child, $seenIds);
        }
    }

    /**
     * @throws CategoryCannotContainItself
     * @throws DuplicateCategoryTreeId
     */
    private function assertTreeIdsAreUnique(CategoryId $rootId, ChildCategories $children): void
    {
        $seenIds = [
            $rootId->toString() => true,
        ];

        foreach ($children as $child) {
            if ($rootId->equals($child->id())) {
                throw CategoryCannotContainItself::becauseIdsMatch($rootId->toString());
            }

            $this->collectIds($child, $seenIds);
        }
    }
}
