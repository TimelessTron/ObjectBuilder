<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\Tests\ClassBuilder;


use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Timelesstron\ObjectBuilder\ClassBuilder\InterfaceBuilder;
use Timelesstron\ObjectBuilder\Exceptions\InfinityInterfaceException;
use Timelesstron\ObjectBuilder\ObjectBuilder;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Entity\Address;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Interface\EmptyTestInterface;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Interface\InfinityInterface;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Interface\MyInterface;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Interface\Selectable;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Interface\SimpleReturnObjectTestInterface;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Interface\SimpleReturnValueTestInterface;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Interface\SimpleTestInterface;

class InterfaceBuilderTest extends TestCase
{
    public function testEmptyInterface(): void
    {
        $interfaceClass = (new InterfaceBuilder())->build(
            new ReflectionClass(EmptyTestInterface::class),
            []
        );

        $this->assertInstanceOf(EmptyTestInterface::class, $interfaceClass);
    }

    public function testInterfaceWithoutReturnValue(): void
    {
        $interfaceClass = (new InterfaceBuilder())->build(
            new ReflectionClass(SimpleTestInterface::class),
            []
        );

        $this->assertInstanceOf(SimpleTestInterface::class, $interfaceClass);
        $this->assertNull($interfaceClass->get());
        $this->assertNull($interfaceClass::post());
    }

    public function testInterfaceWithReturnValue(): void
    {
        $interfaceClass = (new InterfaceBuilder())->build(
            new ReflectionClass(SimpleReturnValueTestInterface::class),
            []
        );

        $this->assertInstanceOf(SimpleReturnValueTestInterface::class, $interfaceClass);

        $this->assertIsArray($interfaceClass->getArray());
        $this->assertIsInt($interfaceClass->getInt());
        $this->assertIsString($interfaceClass->getString());
        $this->assertIsFloat($interfaceClass->getFloat());
        $this->assertIsBool($interfaceClass->getBool());
        $this->assertTrue(is_null($interfaceClass->getMixed()) || !is_null($interfaceClass->getMixed()));
        $this->assertTrue(is_int($interfaceClass->getRandom()) || is_string($interfaceClass->getRandom()));
    }

    public function testInterfaceWithGivenReturnValue(): void
    {
        $interfaceClass = (new InterfaceBuilder())->build(
            new ReflectionClass(SimpleReturnValueTestInterface::class),
            [
                'getString' => 'testString'
            ]
        );

        $this->assertInstanceOf(SimpleReturnValueTestInterface::class, $interfaceClass);

        $this->assertEquals('testString', $interfaceClass->getString());
    }

    public function testInterfaceReturnAnObject(): void
    {
        $interfaceClass = (new InterfaceBuilder())->build(
            new ReflectionClass(SimpleReturnObjectTestInterface::class),
            []
        );

        $this->assertInstanceOf(SimpleReturnObjectTestInterface::class, $interfaceClass);
        $this->assertInstanceOf(Address::class, $interfaceClass->getAddress());
    }

    public function testInterfaceReturnAnObjectWithGivenParameters(): void
    {
        $interfaceClass = ObjectBuilder::init(
            SimpleReturnObjectTestInterface::class,
            [
                'getAddress' => [
                    'street' => 'Berliner Straße',
                    'city' => 'Hamburg',
                ],
            ],
        )->build();

        $this->assertInstanceOf(Address::class, $interfaceClass->getAddress());
        $this->assertSame('Berliner Straße', $interfaceClass->getAddress()->getStreet());
        $this->assertSame('Hamburg', $interfaceClass->getAddress()->getCity());
    }

    public function testInterfaceReturnAnObjectWithGivenParameterObject(): void
    {
        $address = new Address(
            street: 'street',
            zip: '12345',
            city: 'Berlin',
            country: 'DE',
            mainResidence: true,
        );

        $interfaceClass = ObjectBuilder::init(
            SimpleReturnObjectTestInterface::class,
            [
                'getAddress' => $address,
            ],
        )->build();

        $this->assertInstanceOf(Address::class, $interfaceClass->getAddress());
        $this->assertEquals($address, $interfaceClass->getAddress());

        $address = ObjectBuilder::init(Address::class,
            [
                'street' => 'Berliner Straße',
                'city' => 'Hamburg',
            ],
        )->build();

        $interfaceClass = ObjectBuilder::init(
            SimpleReturnObjectTestInterface::class,
            [
                'getAddress' => $address,
            ],
        )->build();

        $this->assertInstanceOf(Address::class, $interfaceClass->getAddress());
        $this->assertSame('Berliner Straße', $interfaceClass->getAddress()->getStreet());
        $this->assertSame('Hamburg', $interfaceClass->getAddress()->getCity());
    }

    public function testInfinityInterface(): void
    {
        $this->expectException(InfinityInterfaceException::class);

        ObjectBuilder::init(InfinityInterface::class)->build();
    }

    public function testAndReturnValues(): void
    {
        // todo dieser test geht nicht.
        $this->assertInstanceOf(
            MyInterface::class,
            ObjectBuilder::init(MyInterface::class)->build(),
        );
    }

    public function testMy(): void
    {
        // todo dieser test geht nicht.
        $this->assertInstanceOf(
            Selectable::class,
            ObjectBuilder::init(Selectable::class)->build(),
        );
    }
}
