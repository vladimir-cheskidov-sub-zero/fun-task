# Prompt Log

- Timestamp: `2026-04-01 00:37:03`
- Summary: Reject explicitly empty `--region` values in `categories:menu` and add a regression command test.
- Request:

```text
@src/Bridge/Symfony/Console/Command/CategoriesMenuCommand.php Treating an explicitly empty --region value as UNSPECIFIED weakens the input validation promised by the CLI contract. `php app categories:menu data/categories.json --region=''` currently succeeds and behaves like the option was omitted, even though the accepted values are documented as kg or ru. Please reject the empty string here and keep UNSPECIFIED only for the null/omitted case; add a command test for `--region=''` to lock that down.
```
