<?php

namespace Timelesstron\ObjectBuilder\Tests\ClassBuilder\Helper;

enum MyTestEnumeration: string
{
    case OK = 'OK';
    case WARNING = 'WARNING';
    case ERROR = 'ERROR';
}
