<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\Exceptions;

use DomainException;
use ReflectionClass;
use Throwable;

class UnknownOrBadFormatNotDeclaredClassException extends DomainException
{
    /**
     * @param ReflectionClass<Object> $class
     * @param Throwable $exception
     */
    public function __construct(ReflectionClass $class, Throwable $exception)
    {
        parent::__construct(
            sprintf("The given class: '%s' cant create. Message: %s", $class->getName(), $exception->getMessage()),
        );
    }
}
