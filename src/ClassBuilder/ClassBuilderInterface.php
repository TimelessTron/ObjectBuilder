<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\ClassBuilder;

use ReflectionClass;

interface ClassBuilderInterface
{
    /**
     * @param ReflectionClass<object> $class
     * @param array<string, mixed> $parameters
     *
     * @return mixed
     */
    public function build(ReflectionClass $class, array $parameters): mixed;
}
