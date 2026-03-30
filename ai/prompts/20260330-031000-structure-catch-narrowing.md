# Prompt Log

- User request: сузить `catch (DomainRuleViolation ...)` в гидраторе, чтобы `CategoryDataStructureIsInvalid` не оборачивалось повторно, и усилить тест на structural failure.
- Scope: preserve direct structure exceptions and distinguish them from wrapped domain-rule violations in tests.
