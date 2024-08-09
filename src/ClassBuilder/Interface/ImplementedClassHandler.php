<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder\Interface;

use ReflectionClass;

class ImplementedClassHandler implements HandlerInterface
{

    public function execute(ReflectionClass $reflectionClass, array $parameters): mixed
    {
        $implementedClasses = $this->interfaceImplementedClasses($reflectionClass);

        return new $implementedClasses[array_rand($implementedClasses)]();
    }

    /** return array<int, string> */
    private function interfaceImplementedClasses(ReflectionClass $interface): array
    {
        $implementingClasses = [];
        foreach (get_declared_classes() as $className) {
            if (in_array($interface->getName(), class_implements($className))) {
                $implementingClasses[] = $className;
            }
        }

        return $implementingClasses;
    }

    public static function support(ReflectionClass $reflectionClass): bool
    {
        foreach (get_declared_classes() as $className) {
            if (in_array($reflectionClass->getName(), class_implements($className))) {
                return true;
            }
        }

        return false;
    }
}
