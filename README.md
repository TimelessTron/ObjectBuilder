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
## Interface
Das übergebene Interface wird geladen und daraus dynamisch eine Klasse erzeugt.
Diese liefert das Interface mit den benötigten Methoden zurück und implementiert das Interface.

Der Rückgabewert der Methoden wird ermittelt und den Methoden ein random Wert zugeteilt.
```php
$result = ObjectBuilder::init(MyInterface::class)->build();
// returns Object of MyInterfaceClass
$value = $result->myMethode()
// returns random value of his return type.
```
Du kannst bestimmen welche Werte die Methoden zurückliefern.
Dazu übergibst du ein Array mit dem Methodennamen als key.
```php
$options = [
    'getMyString' => 'testString'
];

$result = ObjectBuilder::init(MyInterface::class, $options)->build();
// returns Object of MyInterfaceClass
$value = $result->getMyString()
// returns 'testString'
```
Gibt die Methode ein Object zurück in dem du werte setzen möchtest, geht das auch.
```php
$options = [
    'getMyObject' => new SomeObject('Gustav', 27)
];

$result = ObjectBuilder::init(MyInterface::class, $options)->build();
// returns Object of MyInterfaceClass
$value = $result->getMyObject()
/** returns class SomeObject {
 *      string $name => "Gustav",
 *      int $age => 27,
 * }
 * 
```
Es ist auch möglich die Parameter einzeln an das Object weiterzureichen.
```php
$options = [
    'getMyObject' => ['name' => 'Bernhard']
];

$result = ObjectBuilder::init(MyInterface::class, $options)->build();
// returns Object of MyInterfaceClass
$value = $result->getMyObject()
/** returns class SomeObject {
 *      string $name => "Bernhard",
 *      int $age => 27356453,
 * }
 * 
```
