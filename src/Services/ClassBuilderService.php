<?php

namespace Timelesstron\ObjectBuilder\Services;

use ReflectionClass;
use Timelesstron\ObjectBuilder\ClassBuilder\ClassBuilderInterface;
use Timelesstron\ObjectBuilder\ClassBuilder\EnumBuilder;
use Timelesstron\ObjectBuilder\ClassBuilder\TraitBuilder;

class ClassBuilderService
{
    public static function getClassBuilder(ReflectionClass $reflection): ClassBuilderInterface
    {
         return match (true) {
            $reflection->isEnum() => new EnumBuilder(),
            $reflection->isTrait() => new TraitBuilder(),
        };
    }
}
