<?php

declare(strict_types=1);

namespace FunTask\Domain\Category;

use FunTask\Domain\Category\Exception\CategoryAlreadyHasTag;
use FunTask\Domain\Category\Exception\CategoryDoesNotHaveTag;
use FunTask\Domain\Category\Exception\InvalidTagCollectionItem;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Tag>
 */
final class CategoryTags implements IteratorAggregate
{
    /**
     * @var Tag[]
     */
    private array $tags;

    /**
     * @param Tag[] $tags
     *
     * @throws CategoryAlreadyHasTag
     * @throws InvalidTagCollectionItem
     */
    public function __construct(array $tags)
    {
        $indexedTags = [];

        foreach ($tags as $tag) {
            if (!$tag instanceof Tag) {
                throw InvalidTagCollectionItem::becauseItemHasInvalidType();
            }

            $tagValue = $tag->value();
            if (array_key_exists($tagValue, $indexedTags)) {
                throw CategoryAlreadyHasTag::becauseTagIsAlreadyAssigned($tagValue);
            }

            $indexedTags[$tagValue] = $tag;
        }

        $this->tags = array_values($indexedTags);
    }

    /**
     * @throws CategoryAlreadyHasTag
     * @throws InvalidTagCollectionItem
     */
    public function add(Tag $tag): self
    {
        if ($this->contains($tag)) {
            throw CategoryAlreadyHasTag::becauseTagIsAlreadyAssigned($tag->value());
        }

        $updatedTags = $this->tags;
        $updatedTags[] = $tag;

        return new self($updatedTags);
    }

    public function contains(Tag $tag): bool
    {
        foreach ($this->tags as $currentTag) {
            if ($currentTag->equals($tag)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws CategoryDoesNotHaveTag
     * @throws InvalidTagCollectionItem
     */
    public function remove(Tag $tag): self
    {
        if (!$this->contains($tag)) {
            throw CategoryDoesNotHaveTag::becauseTagIsMissing($tag->value());
        }

        $updatedTags = [];
        foreach ($this->tags as $currentTag) {
            if (!$currentTag->equals($tag)) {
                $updatedTags[] = $currentTag;
            }
        }

        return new self($updatedTags);
    }

    /**
     * @return Tag[]
     */
    public function all(): array
    {
        return $this->tags;
    }

    /**
     * @return Traversable<int, Tag>
     */
    public function getIterator(): Traversable
    {
        yield from $this->tags;
    }
}
