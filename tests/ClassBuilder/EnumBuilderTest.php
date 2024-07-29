<?php

namespace Timelesstron\ObjectBuilder\Tests\ClassBuilder;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Timelesstron\ObjectBuilder\ObjectBuilder;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\MyTestEnumeration;

class EnumBuilderTest extends TestCase
{
    public function testEnumBuilderWithoutParameters(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->assertContains(
                ObjectBuilder::init(MyTestEnumeration::class)->build(),
                MyTestEnumeration::cases()
            );
        }
    }

    public function testEnumBuilderWithOneParameter(): void
    {
        $this->assertSame(
            MyTestEnumeration::OK,
            ObjectBuilder::init(MyTestEnumeration::class, ['OK'])->build()
        );
    }

    public function testEnumBuilderWithMultipleParameters(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->assertContains(
                ObjectBuilder::init(MyTestEnumeration::class, ['WARNING', 'ERROR'])->build(),
                [MyTestEnumeration::WARNING, MyTestEnumeration::ERROR]
            );
        }
    }

    public function testEnumBuilderThrowAnExceptionByGivenAWrongParameter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid parameter given vor enum MyTestEnumeration: "NOT_EXIST_KEY".'
        );

        ObjectBuilder::init(MyTestEnumeration::class, ['NOT_EXIST_KEY'])->build();
    }
}
