- build-menu-region-enum
Метод Code snippet from lines 28-31 from @src/Application/Category/BuildMenu.php:
```PHP
    public function region(): string
    {
        return $this->region;
    }
```
должен возвращать уже готовое перечисление. Команда/Запрос могут сами преобразовывать входные данные в доменные типы.
