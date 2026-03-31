# Prompt Log

- Summary: Rename `MenuCategoryVisibilityPolicy` to `MenuCategoryVisibilitySpecification`, move visibility context into the specification object, and keep `MenuBuilderVisitor` focused on traversal/building.
- User request: "Для более чистой архитектуры переименуй @src/Domain/Category/MenuCategoryVisibilityPolicy.php в `MenuCategoryVisibilitySpecification`. * Cделать контекст частью самого объекта, чтобы не передавать 3 параметра в каждый вызов: new MenuCategoryVisibilitySpecification(bool $adultEnabled, bool $staffEnabled, Region $region) и затем isSatisfiedBy(Category $category): bool. * Тогда: Category остаётся источником фактов о себе, Specification выражает menu-specific правило, Visitor только обходит дерево и строит результат."
