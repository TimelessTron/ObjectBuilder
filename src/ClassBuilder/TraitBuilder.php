<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder;

use ReflectionClass;

class TraitBuilder implements ClassBuilderInterface
{

    public function build(ReflectionClass $class, array $parameters): mixed
    {
        $anonClassWithTrait = sprintf(
            'return new class { use %s; };',
            $class->getName()
        );

        return eval($anonClassWithTrait);
    }
}