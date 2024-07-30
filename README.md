# ObjectBuilder
For automatically creating objects. Objects are created with random values
## Enumeration
```php
enum MyEnumeration: string
{
    case OK = 'OK';
    case WARNING = 'WARNING';
    case ERROR = 'ERROR';
}
```
```php
$result = ObjectBuilder::init(MyEnumeration::class)->build();
// returns one of MyEnumeration cases
```
Du kannst bei einem Enum den Wert bestimmen der Verwendet werden soll.
```php
$result = ObjectBuilder::init(MyEnumeration::class, ['OK'])->build();
// returns MyEnumeration::OK

$result = ObjectBuilder::init(MyEnumeration::class, ['WARNING', 'ERROR'])->build();
// returns one of MyEnumeration::WARNING|MyEnumeration::ERROR
```
## Trait
Für übergebene Traits wird eine anonyme Klasse erzeugt die den Trait verwendet.
Übergebene Parameter werden vom TraitBuilder nicht berücksichtigt.
```php
$result = ObjectBuilder::init(MyTrait::class)->build();
// returns {class@anonymous/...}
```
