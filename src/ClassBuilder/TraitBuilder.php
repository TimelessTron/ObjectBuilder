<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder;

use ReflectionClass;

class TraitBuilder implements ClassBuilderInterface
{

    /**
     * @param ReflectionClass<Object> $class
     * @param array<string, mixed> $parameters
     */
    public function build(ReflectionClass $class, array $parameters): object
    {
        $anonClassWithTrait = sprintf(
            'return new class { use %s; };',
            $class->getName()
        );

        return eval($anonClassWithTrait);
    }
}