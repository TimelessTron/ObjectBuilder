<?php

namespace Timelesstron\ObjectBuilder\Exceptions;

use DomainException;
use Throwable;

class ObjectBuilderReflectionException extends DomainException
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct(
            $previous->getMessage(),
            $previous->getCode(),
            $previous
        );
    }
}
