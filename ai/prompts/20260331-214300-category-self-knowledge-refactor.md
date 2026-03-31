# Prompt Log

- Summary: Move tag-type and restricted-visibility knowledge from `MenuBuilderVisitor` into `Category` so the aggregate is the source of truth about itself.
- User request: "перенеси эти методы ... в @src/Domain/Category/Category.php. Именно @src/Domain/Category/Category.php должна служить источником знаний о самой себе."
- Additional requirement: Move regional visibility into `Category` as well, so `MenuBuilderVisitor` no longer knows about `TagType::REGION()`.
