<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\ClassBuilder;

use ReflectionClass;

class TraitBuilder implements ClassBuilderInterface
{
    /**
     * @param ReflectionClass<object> $class
     * @param array<string, mixed> $parameters
     */
    public function build(ReflectionClass $class, array $parameters): object
    {
        $anonClassWithTrait = sprintf('return new class { use %s; };', $class->getName());

        return eval($anonClassWithTrait);
    }
}
