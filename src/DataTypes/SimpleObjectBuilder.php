<?php

namespace Timelesstron\ObjectBuilder\DataTypes;


use Timelesstron\ObjectBuilder\Dto\Property;

class SimpleObjectBuilder implements DataTypeInterface
{

    public function build(): mixed
    {
        return (object)[];
    }

    public function setProperty(Property $property): self
    {
        // TODO: Implement setProperty() method.

        return $this;
    }

    public function buildAsString(): string
    {
        return '(object)[]';
    }
}
