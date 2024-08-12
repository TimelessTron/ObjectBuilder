<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder\Dto;

class Parameters
{
    /**
     * @param array<string|int, mixed> $parameter
     */
    public function __construct(
        public readonly array $parameter
    ) {}
}
