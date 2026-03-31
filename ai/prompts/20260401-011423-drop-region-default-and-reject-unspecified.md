# Prompt Log

- Timestamp: `2026-04-01 01:14:23`
- Summary: Drop the `--region` default sentinel from the CLI contract and reject `--region=unspecified`.
- Request:

```text
lines 43 from @src/Bridge/Symfony/Console/Command/CategoriesMenuCommand.php Using `CategoryRegion::UNSPECIFIED()->getValue()` as the default means Symfony never passes `null` for an omitted `--region`, so the `null` branch in `normalizeRegionOption()` is effectively dead and `--region=unspecified` still succeeds. That keeps the undocumented sentinel value in the public CLI contract, which is exactly what the task was trying to avoid. Please drop the default here so omission maps to `null`, and add a regression test for an explicit `--region=unspecified`.
```
