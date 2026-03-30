# Prompt Log

## User Prompt

Code snippet from lines 120-145 from @src/Domain/Category/Tag.php:
```PHP
    /**
     * @throws UnknownRegionTagValue
     */
    private function assertRegionValueIsSupported(string $parameter): void
    {
        try {
            Region::assertValidValue($parameter);
        } catch (\UnexpectedValueException $exception) {
            throw UnknownRegionTagValue::becauseValueIsUnsupported($parameter, $exception);
        }

        if ($parameter === Region::UNSPECIFIED()->getValue()) {
            throw UnknownRegionTagValue::becauseValueIsUnsupported($parameter);
        }
    }
    /**
     * @throws UnknownRestrictedTagValue
     */
    private function assertRestrictedValueIsSupported(string $parameter): void
    {
        try {
            RestrictedVisibility::assertValidValue($parameter);
        } catch (\UnexpectedValueException $exception) {
            throw UnknownRestrictedTagValue::becauseValueIsUnsupported($parameter, $exception);
        }
    }
```
Упрости эти два метода заменив `try/catch` на `if(Enum::isValid(...))`
