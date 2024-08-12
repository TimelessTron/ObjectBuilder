<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\DataTypes;

use InvalidArgumentException;
use Timelesstron\ObjectBuilder\ClassBuilder\Dto\NoValueSet;
use Timelesstron\ObjectBuilder\Dto\Property;

class BooleanBuilder implements DataTypeInterface
{
    private ?Property $property = null;

    public function build(): bool
    {
        if ($this->property instanceof Property && !$this->property->value instanceof NoValueSet) {

            return $this->property->value;
        }

        return (bool)mt_rand(0, 1);
    }

    public function setProperty(Property $property): self
    {
        if (!is_bool($property->value) && $property->value !== null) {
            throw new InvalidArgumentException(
                sprintf('Value "%s" must be an boolean. %s given', $property->value, gettype($property->value))
            );
        }

        $this->property = $property;

        return $this;
    }

    public function buildAsString(): string
    {
        return $this->build() ? 'true' : 'false';
    }
}
