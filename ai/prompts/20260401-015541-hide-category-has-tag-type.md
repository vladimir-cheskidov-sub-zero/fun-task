# Prompt Log

- Timestamp: `2026-04-01 01:55:41`
- Summary: Hide `Category::hasTagType()` and move `VisibilityAuditVisitor` to explicit category predicates.
- Request:

```text
@src/Domain/Category/Visitor/VisibilityAuditVisitor.php игнорирует, что @src/Domain/Category/Category.php содержит или должна содержать все нужные предикаты для того, чтобы классу @src/Domain/Category/Visitor/VisibilityAuditVisitor.php не пришлось использовать  Code snippet from lines 121 from @src/Domain/Category/Category.php:
```PHP
    public function hasTagType(TagType $tagType): bool
``` . Сделай метод Code snippet from lines 121 from @src/Domain/Category/Category.php:
```PHP
    public function hasTagType(TagType $tagType): bool
```  приватным.
```
