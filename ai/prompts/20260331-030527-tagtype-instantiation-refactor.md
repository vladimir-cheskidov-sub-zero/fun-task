Code snippet from lines 138-152 from @src/Domain/Category/Tag.php:
```PHP
        $supportedTypes = [
            TagType::ROOT()->getValue(),
            TagType::MENU()->getValue(),
            TagType::PROMO()->getValue(),
            TagType::HIDDEN()->getValue(),
            TagType::SEARCHABLE()->getValue(),
            TagType::RESTRICTED()->getValue(),
            TagType::REGION()->getValue(),
        ];
        if (!in_array($rawType, $supportedTypes, true)) {
            throw UnknownTagType::becauseTypeIsUnsupported($rawType);
        }
        return TagType::from($rawType);
```
Оптимизируй этот код. Пусть значение сразу передается конструктор TagType, а UnknownTagType::becauseTypeIsUnsupported пусть бросается только если TagType не сможет инстанциироваться
