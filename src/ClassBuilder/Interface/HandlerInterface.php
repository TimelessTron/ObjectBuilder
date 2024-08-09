<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder\Interface;

use ReflectionClass;

interface HandlerInterface
{
    /** @param array<string, string|array> $parameters */
    public function execute(ReflectionClass $reflectionClass, array $parameters): mixed;

    public static function support(ReflectionClass $reflectionClass): bool;
}
