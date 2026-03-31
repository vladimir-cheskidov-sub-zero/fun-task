# Prompt Log

- Summary: Move menu-renderability decision from `MenuBuilderVisitor` into `MenuCategoryVisibilityPolicy`, keeping `Category` as the source of facts about itself and the visitor focused on traversal/building.
- User request: "Вынеси итоговое правило в policy MenuCategoryVisibilityPolicy, оставив Category источником фактов о себе. Цель более чистая архитектура."
