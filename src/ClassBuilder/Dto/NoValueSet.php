<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\ClassBuilder\Dto;

final class NoValueSet
{
    public function __toString(): string
    {
        return 'null';
    }
}
