<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\ClassBuilder\Dto;

use Timelesstron\ObjectBuilder\Exceptions\ObjectBuilderWrongClassesGivenException;

class DataType
{
    public readonly bool $isObject;

    public function __construct(
        public readonly ?string $namespace,
        public readonly mixed $type,
        public readonly mixed $value,
        public readonly bool $isValueGiven,
    ) {
        $this->isObject = is_object($value);

        if ($this->isObject && !str_ends_with($value::class, $type)) {
            throw new ObjectBuilderWrongClassesGivenException(
                sprintf('Given wrong class for return type. Given: %s. Expected: %s', $value::class, $type),
            );
        }
    }
}
