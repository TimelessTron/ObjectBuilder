<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder\Dto;

final class NoValueSet
{
    public function __toString(): string
    {
        return 'null';
    }
}
