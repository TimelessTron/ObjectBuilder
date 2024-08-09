<?php

namespace Timelesstron\ObjectBuilder\DataTypes;

use InvalidArgumentException;
use Timelesstron\ObjectBuilder\ClassBuilder\Dto\NoValueSet;
use Timelesstron\ObjectBuilder\Dto\Property;

class ArrayBuilder implements DataTypeInterface
{

    private ?Property $property = null;

    public function build(): array
    {
        if ($this->property instanceof Property && !$this->property->value instanceof NoValueSet) {

            return $this->property->value;
        }

        return ['a' => 13];
    }

    public function setProperty(Property $property): self
    {
        if(!is_array($property->value) && null !== $property->value){
            throw new InvalidArgumentException(
                sprintf(
                    'Value "%s" must be an array. %s given',
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
        return var_export($this->build(), true);
    }
}
