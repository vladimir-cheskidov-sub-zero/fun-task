Code snippet from lines 53-66 from @src/Domain/Category/Visitor/SearchIndexExportVisitor.php:
```PHP
    /**
     * @return array<int, \FunTask\Domain\Category\Region>
     */
    private function collectRegions(Category $category): array
    {
        $regions = [];
        foreach ($category->tags() as $tag) {
            if (!$tag->isOfType(TagType::REGION())) {
                continue;
            }
            $regions[] = $tag->region();
        }
        return $regions;
    }
```
данный метод берет на себя ответственность получить знания о @src/Domain/Category/Category.php не спрашивая об этих знаниях саму @src/Domain/Category/Category.php .
