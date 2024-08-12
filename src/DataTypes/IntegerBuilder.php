<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\DataTypes;

use InvalidArgumentException;
use Timelesstron\ObjectBuilder\ClassBuilder\Dto\NoValueSet;
use Timelesstron\ObjectBuilder\Dto\Property;

class IntegerBuilder implements DataTypeInterface
{
    private ?Property $property = null;

    public function build(): int
    {
        if ($this->property instanceof Property && !$this->property->value instanceof NoValueSet) {

            return $this->property->value;
        }

        return mt_rand();
    }

    public function setProperty(Property $property): self
    {
        if (!is_int($property->value) && $property->value !== null) {
            throw new InvalidArgumentException(
                sprintf('Value "%s" must be an integer. %s given', $property->value, gettype($property->value))
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
