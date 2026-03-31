# Prompt Log

- Summary: Refactor `MenuBuilderVisitor` to remove duplicated tag constants and use domain enums/value objects instead of raw tag strings.
- User request: "Проведи рефакторинг @src/Domain/Category/Visitor/MenuBuilderVisitor.php. Требуется: избавиться от наличия дублирующих источников истины (констант); MenuBuilderVisitor не должен игнорировать доменную модель - нужно использование функционала уже созданных перечислений домена."
