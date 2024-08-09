<?php

namespace Timelesstron\ObjectBuilder\DataTypes;

use Timelesstron\ObjectBuilder\Dto\Property;

interface DataTypeInterface
{
    public function build(): mixed;

    public function setProperty(Property $property): self;

    public function buildAsString(): string;
}
