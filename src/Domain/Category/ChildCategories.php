<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use FunTask\Domain\Category\Exception\CategoryChildWasNotFound;
use FunTask\Domain\Category\Exception\InvalidChildCategoryItem;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Category>
 */
final class ChildCategories implements IteratorAggregate
{
    /**
     * @var Category[]
     */
    private array $children;

    /**
     * @param Category[] $children
     *
     * @throws InvalidChildCategoryItem
     */
    public function __construct(array $children)
    {
        foreach ($children as $child) {
            if (!$child instanceof Category) {
                throw InvalidChildCategoryItem::becauseItemHasInvalidType();
            }
        }

        $this->children = array_values($children);
    }

    /**
     * @throws InvalidChildCategoryItem
     */
    public function add(Category $child): self
    {
        $updatedChildren = $this->children;
        $updatedChildren[] = $child;

        return new self($updatedChildren);
    }

    /**
     * @throws CategoryChildWasNotFound
     * @throws InvalidChildCategoryItem
     */
    public function removeById(CategoryId $childId): self
    {
        $updatedChildren = [];
        $isRemoved = false;

        foreach ($this->children as $child) {
            if ($child->id()->equals($childId)) {
                $isRemoved = true;
                continue;
            }

            $updatedChildren[] = $child;
        }

        if (!$isRemoved) {
            throw CategoryChildWasNotFound::becauseChildIdIsUnknown($childId->toString());
        }

        return new self($updatedChildren);
    }

    /**
     * @return Category[]
     */
    public function all(): array
    {
        return $this->children;
    }

    /**
     * @return Traversable<int, Category>
     */
    public function getIterator(): Traversable
    {
        yield from $this->children;
    }
}
