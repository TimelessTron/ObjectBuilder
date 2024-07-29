<?php

namespace Timelesstron\ObjectBuilder;

use ReflectionClass;
use ReflectionException;
use Timelesstron\ObjectBuilder\ClassBuilder\ClassBuilderInterface;
use Timelesstron\ObjectBuilder\Exceptions\ObjectBuilderReflectionException;
use Timelesstron\ObjectBuilder\Services\ClassBuilderService;

final class ObjectBuilder
{
    private ReflectionClass $reflection;

    private ClassBuilderInterface $classBuilder;
    private function __construct(
        string $className,
        /** @var array<string, string|array> */
        private readonly array $parameters,
    ) {
        $this->reflection = $this->newReflectionClass($className);
        $this->classBuilder = ClassBuilderService::getClassBuilder($this->reflection);
    }

    public static function init(string $className, array $parameters = []): self
    {
        return new self($className, $parameters);
    }

    public function build(): object
    {
        return $this->classBuilder->build(
            $this->reflection,
            $this->parameters
        );
    }

    /** @param class-string $className */
    public function newReflectionClass(string $className): ReflectionClass
    {
        try {
            return new ReflectionClass($className);
        } catch (ReflectionException $exception) {
            throw new ObjectBuilderReflectionException($exception);
        }
    }
}
