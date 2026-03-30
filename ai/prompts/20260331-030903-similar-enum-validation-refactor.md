Найди похожие участки кода и проведи аналогичный рефакторинг. Например тут Code snippet from lines 125-131 from @src/Domain/Category/Tag.php:
```PHP
        $supportedValues = [
            Region::KG()->getValue(),
            Region::RU()->getValue(),
        ];
        if (!in_array($parameter, $supportedValues, true)) {
            throw UnknownRegionTagValue::becauseValueIsUnsupported($parameter);
        }
```
