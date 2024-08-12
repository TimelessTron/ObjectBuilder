<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\DataTypes;

use Timelesstron\ObjectBuilder\Dto\Property;

class NullBuilder implements DataTypeInterface
{
    public function build(): mixed
    {
        return null;
    }

    public function setProperty(Property $property): self
    {
        return $this;
    }

    public function buildAsString(): string
    {
        return 'null';
    }
}
