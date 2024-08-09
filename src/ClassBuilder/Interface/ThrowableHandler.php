<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder\Interface;

use Exception;
use ReflectionClass;
use Timelesstron\ObjectBuilder\ObjectBuilder;

final class ThrowableHandler implements HandlerInterface
{
    public function execute(ReflectionClass $reflectionClass, array $parameters): object
    {
        return ObjectBuilder::init(Exception::class, [
            ...$parameters,
            'previous' => null,
        ])->build();
    }

    public static function support(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->getName() === 'Throwable';
    }
}
