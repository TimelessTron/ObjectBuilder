<?php

namespace Timelesstron\ObjectBuilder\Services;

use ReflectionClass;
use Timelesstron\ObjectBuilder\ClassBuilder\ClassBuilderInterface;

class ClassBuilderService
{
    public static function getClassBuilder(ReflectionClass $reflection): ClassBuilderInterface
    {
        // ToDo: Add Builders for all types of Classes
    }
}
