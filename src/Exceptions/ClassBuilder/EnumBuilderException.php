<?php

namespace Timelesstron\ObjectBuilder\Exceptions\ClassBuilder;


use DomainException;

class EnumBuilderException extends DomainException
{

    /**
     * @param string $message
     */
    public function __construct(
        string $message
    ) {
        parent::__construct($message);
    }
}
