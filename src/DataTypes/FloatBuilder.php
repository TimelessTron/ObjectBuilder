<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\DataTypes;

use InvalidArgumentException;
use Timelesstron\ObjectBuilder\ClassBuilder\Dto\NoValueSet;
use Timelesstron\ObjectBuilder\Dto\Property;

class FloatBuilder implements DataTypeInterface
{
    private ?Property $property = null;

    public function build(): float
    {
        if ($this->property instanceof Property && !$this->property->value instanceof NoValueSet) {

            return $this->property->value;
        }

        return mt_rand() / mt_getrandmax();
    }

    public function setProperty(Property $property): self
    {
        if (!is_float($property->value) && null !== $property->value) {
            throw new InvalidArgumentException(
                sprintf(
                    'Value "%s" must be an float. %s given',
                    $property->value,
                    gettype($property->value)
                )
            );
        }

        $this->property = $property;

        return $this;
    }

    public function buildAsString(): string
    {
        return (string)$this->build();
    }
}
