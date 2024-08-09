<?php

namespace Timelesstron\ObjectBuilder\Services;

use Timelesstron\ObjectBuilder\DataTypes\ArrayBuilder;
use Timelesstron\ObjectBuilder\DataTypes\BooleanBuilder;
use Timelesstron\ObjectBuilder\DataTypes\CallbackBuilder;
use Timelesstron\ObjectBuilder\DataTypes\DataTypeInterface;
use Timelesstron\ObjectBuilder\DataTypes\FloatBuilder;
use Timelesstron\ObjectBuilder\DataTypes\IntegerBuilder;
use Timelesstron\ObjectBuilder\DataTypes\MixedBuilder;
use Timelesstron\ObjectBuilder\DataTypes\NullBuilder;
use Timelesstron\ObjectBuilder\DataTypes\SimpleObjectBuilder;
use Timelesstron\ObjectBuilder\DataTypes\StringBuilder;

class DataTypeService
{
    public static function getDataTypeBuilder(?string $type): ?DataTypeInterface
    {
        return match ($type) {
            'int' => new IntegerBuilder(),
            'float' => new FloatBuilder(),
            'string' => new StringBuilder(),
            'bool' => new BooleanBuilder(),
            'array' => new ArrayBuilder(),
            'mixed' => new MixedBuilder(),
            'object' => new SimpleObjectBuilder(),
            'callback', 'callable' => new CallbackBuilder(),
            null, '?', 'null' => new NullBuilder(),

            default => null,
        };
    }

    public static function getDataTypeFromString(?string $dataType): ?array
    {
        return match (true) {
            null === $dataType => null,
            $dataType[0] === '?' => ['?', ltrim($dataType, '?')], //[array_rand([0, 1])],
            str_contains($dataType, '|') => explode('|', $dataType), //[array_rand(explode('|', $dataType))],

            default => [$dataType],
        };
    }
}
