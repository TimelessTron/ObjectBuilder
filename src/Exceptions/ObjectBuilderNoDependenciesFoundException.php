<?php

namespace Timelesstron\ObjectBuilder\Exceptions;

use DomainException;

class ObjectBuilderNoDependenciesFoundException extends DomainException
{
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
