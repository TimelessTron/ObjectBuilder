<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\ClassBuilder\Dto;

class Methode
{
    public function __construct(
        public readonly string $content,
        public readonly DeclarationEnum $declaration,
        public readonly bool $isStatic,
        public readonly string $name,
        public readonly Parameters $parameters,
        public readonly ?DataType $returnValue,
    ) {
    }
}
