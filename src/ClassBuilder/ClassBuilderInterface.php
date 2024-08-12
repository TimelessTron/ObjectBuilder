<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder;

use ReflectionClass;

interface ClassBuilderInterface
{
    /**
     * @param ReflectionClass<Object> $class
     * @param array<string, mixed> $parameters
     *
     * @return mixed
     */
    public function build(ReflectionClass $class, array $parameters): mixed;
}
