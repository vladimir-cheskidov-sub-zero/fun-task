Создай `VisibilityAuditVisito`r согласно @ai/specification.md
Создай Application layer use case, который будет вызывать `VisibilityAuditVisitor`
Создай entripoint, который будет вызывать use case
Ограничения:
* Visitor должен быть частью домена - в директории `src/Domain/Category/Visitor`
* Use case не должен экспонировать домен
* Use case должен map-ить все доменные исключения в исключения application слоя (вероятнее всего портребуется только одно исключение application слоя со статическими фабричными методами)
* Если use case будет возвращать сложные данные, то они должны быть оформлены как DTO. DTO должно лежать в директории `srs/Application/Dto`. Там же должен лежать и Assembler, который будет преобразовывать собранные Visitor-ом данные в DTO
* Use case должен иметь только один метод: `execute` с одним параметром `<Query class name typehint> $query`
* Есди Use case query class-у потребуется принимать в конструктор перечисления, то нельзя использовать перечисления домена - это должны быть перечисления application слоя, которые при необходимости map-ятся в доменные перечисления
* Используй @src/Domain/Category/Visitor/MenuBuilderVisitor.php  и связанные с ним классы как пример стиля и ограничений
