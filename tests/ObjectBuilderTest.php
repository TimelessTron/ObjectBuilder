<?php

namespace Timelesstron\ObjectBuilder\Tests;

use ReflectionClass;
use Timelesstron\ObjectBuilder\ClassBuilder\ClassBuilderInterface;
use Timelesstron\ObjectBuilder\Exceptions\ObjectBuilderReflectionException;
use Timelesstron\ObjectBuilder\ObjectBuilder;
use PHPUnit\Framework\TestCase;
use Timelesstron\ObjectBuilder\Services\ClassBuilderService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ObjectBuilderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private ClassBuilderInterface|Mockery\LegacyMockInterface $mockClassBuilder;

    protected function setUp(): void
    {
        $this->mockClassBuilder = Mockery::mock(ClassBuilderInterface::class);

        Mockery::mock('alias:' . ClassBuilderService::class)
            ->shouldReceive('getClassBuilder')
            ->andReturn($this->mockClassBuilder);
    }

    public function testInit(): void
    {
        $objectBuilder = ObjectBuilder::init('stdClass', []);
        $this->assertInstanceOf(ObjectBuilder::class, $objectBuilder);
    }

    public function testBuild(): void
    {
        $className = 'stdClass';
        $parameters = [];

        $this->mockClassBuilder->shouldReceive('build')
            ->once()
            ->with(Mockery::type(ReflectionClass::class), $parameters)
            ->andReturn(new $className);

        $objectBuilder = ObjectBuilder::init($className, $parameters);
        $object = $objectBuilder->build();

        $this->assertInstanceOf($className, $object);
    }

    public function testNewReflectionClassThrowsException(): void
    {
        $this->expectException(ObjectBuilderReflectionException::class);

        $objectBuilder = ObjectBuilder::init('NonExistentClass');
        $objectBuilder->newReflectionClass('NonExistentClass');
    }
}
