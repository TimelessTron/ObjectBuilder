<?php

namespace Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Interface;

use Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Entity\Address;

interface SimpleReturnObjectTestInterface
{
    public function getAddress(): Address;
}
