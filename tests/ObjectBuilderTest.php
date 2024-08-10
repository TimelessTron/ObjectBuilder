<?php

namespace Timelesstron\ObjectBuilder\Tests;

use DateInterval;
use DatePeriod;
use ReflectionClass;
use ReflectionException;
use Timelesstron\ObjectBuilder\ObjectBuilder;
use PHPUnit\Framework\TestCase;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Entity\Address;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Entity\Name;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Entity\Person;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Interface\MyInterface;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\StockClass;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Trait\MyTrait;

class ObjectBuilderTest extends TestCase
{
    public function testTest2(): void
    {
        /** @var Person $person */
        $person = ObjectBuilder::init(
            Person::class,
            [
                'status' => ['WARNING']
            ]
        )->build();

//        var_dump($person);
        $this->assertInstanceOf(Person::class, $person);

    }
    public function testEnum(): void
    {
        /** @var Person $person */
        $person = ObjectBuilder::init(
            Person::class,
            [
                'age' => 25,
                'name' => [
                    'firstName' => 'Max',
                    'lastName' => 'Mustermann'
                ],
                'address' => [
                    'city' => 'Berlin',
                    'country' => null
                ]
            ]
        )->build();


        $this->assertInstanceOf(Person::class, $person);
        $this->assertInstanceOf(Name::class, $person->getName());
        $this->assertInstanceOf(Address::class, $person->getAddress());
        $this->assertEquals('Max', $person->getName()->getFirstName());
        $this->assertEquals('Berlin', $person->getAddress()->getCity());
        $this->assertEquals(null, $person->getAddress()->getCountry());
        $this->assertEquals('Mustermann', $person->getName()->getLastName());
        $this->assertEquals(25, $person->getAge());

    }

    /**
     * @throws ReflectionException
     */
    public function testTrait(): void
    {
        /** @var MyTrait $trait */
        $trait = ObjectBuilder::init(MyTrait::class)->build();
        $class = new ReflectionClass(
            $trait
        );

        $this->assertArrayHasKey(
            MyTrait::class,
            $class->getTraits()
        );

        $array = $trait->toArray('[1,2,3]');
        $this->assertEquals([1,2,3], $array);
        $this->assertSame('Hello', $trait->sayHello());
        $this->assertSame('MyTrait', $trait->trait);
    }

    public function testStockClasses(): void
    {
        /**
         * überlegen ob man auch parameter an das Interface übergeben kann.
         * z.B. wenn der rückgabewert einer Methode Adresse ist...
         * Beim direkten Aufruf des Interfaces müsste man den MethodenNamen als referenz nehmen.
         * Bei Objecten die ein Interface in eine Variable legen, kann man diese nehmen. Dann ist aber immer noch die Frage welche Methode.
         * Also vielleicht in diesen fällen auch auf den MethodenNamen gehen.
         *
         * Object { public MyInterface $interface; }
         *
         * ObjectBuilder::init(MyInterface::class, [ 'interface' => [ 'get' => [~ADRESSDATEN~], 'put' => 'string' ] ])->build();
         * In diesem fall wird es aber wiedr problematisch mit z.B. int|string wenn random int rauskommt, aber ein string übergeben wurde.
         * Man könnte versuchen auch callbacks zu übergeben. da muss geprüft werden wie diese in klartext umgewandelt werden kann.
         *
         * eventuell CreatReturnValue von ClassBuilder auslagern damit auch die anderen Builder darauf zugreifen könnne.
         * das würe gut für das Interface, für Abstracte Klassen und für mixed.
         */
        $stockClasses1 = ObjectBuilder::init(MyInterface::class, [
            'get' => ObjectBuilder::init(Address::class, [
                'street' => 'Leipziger Straße'
            ])->build(),
            'put' => 'Hannes',
            'post' => [
                'street' => 'Bremer Straße'
            ]
        ])->build();
        $stockClasses2 = ObjectBuilder::init(Address::class, [
            'street' => 'Leipziger Straße'
        ])->build();
        $stockClasses3 = ObjectBuilder::init(Person::class)->build();

//        var_dump($stockClasses1);
//        var_dump($stockClasses2);

        $this->assertTrue(true);
    }

    public function testTest(): void
    {
        $classes = get_declared_classes();

        $classesInGlobalNamespace = array_filter($classes, function($class) {
            return strpos($class, '\\') === false;
        });

//        print_r($classesInGlobalNamespace);

        $this->assertTrue(true);

    }

    public function testStockClass(): void
    {
        $stockClass = ObjectBuilder::init(DateInterval::class)->build();
        $this->assertInstanceOf(DateInterval::class, $stockClass);
        $stockClass = ObjectBuilder::init(DatePeriod::class)->build();
        $this->assertInstanceOf(DatePeriod::class, $stockClass);
        $stockClass = ObjectBuilder::init(StockClass::class)->build();
        $this->assertInstanceOf(StockClass::class, $stockClass);
        $stockClass = ObjectBuilder::init(StockClass::class)->build();
        $this->assertInstanceOf(StockClass::class, $stockClass);
        $stockClass = ObjectBuilder::init(StockClass::class)->build();
        $this->assertInstanceOf(StockClass::class, $stockClass);
        $stockClass = ObjectBuilder::init(StockClass::class)->build();
        $this->assertInstanceOf(StockClass::class, $stockClass);
        $stockClass = ObjectBuilder::init(StockClass::class)->build();
        $this->assertInstanceOf(StockClass::class, $stockClass);
    }
}
