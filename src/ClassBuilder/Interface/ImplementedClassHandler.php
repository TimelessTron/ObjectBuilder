<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder\Interface;

use ReflectionClass;

class ImplementedClassHandler implements HandlerInterface
{

    /**
     * @param ReflectionClass<Object> $reflectionClass
     * @param array<string, mixed> $parameters
     */
    public function execute(ReflectionClass $reflectionClass, array $parameters): object
    {
        $implementedClasses = $this->interfaceImplementedClasses($reflectionClass);

        return new $implementedClasses[array_rand($implementedClasses)]();
    }

    /**
     * @param ReflectionClass<Object> $reflectionClass
     *
     * @return array<int, string>
     */
    private function interfaceImplementedClasses(ReflectionClass $reflectionClass): array
    {
        $implementingClasses = [];
        foreach (get_declared_classes() as $className) {
            if (in_array($reflectionClass->getName(), class_implements($className))) {
                $implementingClasses[] = $className;
            }
        }

        return $implementingClasses;
    }

    /**
     * @param ReflectionClass<Object> $reflectionClass
     */
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
