<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder\Services;

use ReflectionClass;
use Timelesstron\ObjectBuilder\ClassBuilder\Interface\FileContentHandler;
use Timelesstron\ObjectBuilder\ClassBuilder\Interface\HandlerInterface;
use Timelesstron\ObjectBuilder\ClassBuilder\Interface\ImplementedClassHandler;
use Timelesstron\ObjectBuilder\ClassBuilder\Interface\ThrowableHandler;
use Timelesstron\ObjectBuilder\Exceptions\InterfaceHandlerNotFoundException;

final class HandlerService
{
    public static function getHandler(ReflectionClass $reflection): HandlerInterface
    {
        return match (true) {
            ThrowableHandler::support($reflection) => new ThrowableHandler(),
            FileContentHandler::support($reflection) => new FileContentHandler(),
            ImplementedClassHandler::support($reflection) => new ImplementedClassHandler(),
            default => throw new InterfaceHandlerNotFoundException()
        };
    }
}