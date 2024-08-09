<?php

namespace Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper;

class PrivateConstructorClass
{
    private function __construct() {}

    public function current(): void {}
}
