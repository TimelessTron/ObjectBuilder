<?php

namespace Timelesstron\ObjectBuilder\Dto;

class Property
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $type,
        public readonly mixed $value,
    ) {
    }
}
