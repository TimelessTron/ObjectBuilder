<?php

namespace Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Entity;

use DateTimeImmutable;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Enums\MyEnum;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Interface\MyInterface;
use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Trait\MyTrait;

class Person
{
    use MyTrait;

    public function __construct(
        private readonly Name $name,
        private readonly int $age,
        private readonly Address $address,
        private readonly MyEnum $status,
        private readonly DateTimeImmutable $birthDate,
        private readonly MyInterface $someInterface,
    ) {
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}
