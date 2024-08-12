<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\ClassBuilder\Interface;

use Exception;
use ReflectionClass;
use Timelesstron\ObjectBuilder\ObjectBuilder;

final class ThrowableHandler implements HandlerInterface
{
    /**
     * @param ReflectionClass<Object> $reflectionClass
     * @param array<string, mixed> $parameters
     */
    public function execute(ReflectionClass $reflectionClass, array $parameters): object
    {
        return ObjectBuilder::init(Exception::class, [
            ...$parameters,
            'previous' => null,
        ])->build();
    }

    /**
     * @param ReflectionClass<Object> $reflectionClass
     */
    public static function support(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->getName() === 'Throwable';
    }
}
