<?php

namespace Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper\Enums;

enum MyEnum: string
{
    case OK = 'OK';
    case WARNING = 'WARNING';
    case ERROR = 'ERROR';
}
