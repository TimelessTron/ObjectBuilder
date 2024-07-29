<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder;

use ReflectionClass;

interface ClassBuilderInterface
{
    /**
     * @param ReflectionClass $class
     * @param array<string, string|array> $parameters
     *
     * @return mixed
     */
    public function build(ReflectionClass $class, array $parameters): mixed;
}
