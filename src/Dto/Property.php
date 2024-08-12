<?php

namespace Timelesstron\ObjectBuilder\Dto;

class Property
{
    /**
     * @param string|null $name
     * @param class-string|string|null $type
     * @param mixed $value
     */
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $type,
        public readonly mixed $value,
    ) {
    }
}
