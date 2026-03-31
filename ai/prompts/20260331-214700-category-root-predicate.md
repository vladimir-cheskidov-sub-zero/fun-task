# Prompt Log

- Summary: Move the technical-root knowledge behind `Category::isRoot()` so `MenuBuilderVisitor` asks the aggregate instead of checking `TagType::ROOT()` directly.
- User request: "This still keeps the 'technical root' rule inside the visitor. The requirement in the follow-up prompt was for `Category` to be the source of truth about itself, so this should become a domain predicate such as `Category::isRoot()` and the visitor should only ask the aggregate instead of knowing about `TagType::ROOT()` directly."
