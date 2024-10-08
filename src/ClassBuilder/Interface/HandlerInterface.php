<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\ClassBuilder\Interface;

use ReflectionClass;

interface HandlerInterface
{
    /**
     * @param ReflectionClass<object> $reflectionClass
     * @param array<string, mixed> $parameters
     */
    public function execute(ReflectionClass $reflectionClass, array $parameters): mixed;

    /**
     * @param ReflectionClass<object> $reflectionClass
     */
    public static function support(ReflectionClass $reflectionClass): bool;
}
