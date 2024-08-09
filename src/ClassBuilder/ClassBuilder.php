<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder;

use App\Module\ObjectBuilder\Exceptions\ClassBuilder\ObjectBuilderDataTypeAndClassNotFoundException;
use DateInterval;
use DatePeriod;
use PHPUnit\Event\InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;
use Throwable;
use Timelesstron\ObjectBuilder\ClassBuilder\Dto\NoValueSet;
use Timelesstron\ObjectBuilder\DataTypes\DataTypeInterface;
use Timelesstron\ObjectBuilder\Dto\Property;
use Timelesstron\ObjectBuilder\Exceptions\ObjectBuilderReflectionException;
use Timelesstron\ObjectBuilder\Exceptions\ObjectBuilderWrongClassesGivenException;
use Timelesstron\ObjectBuilder\Exceptions\UnknownOrBadFormatNotDeclaredClassException;
use Timelesstron\ObjectBuilder\ObjectBuilder;
use Timelesstron\ObjectBuilder\Services\DataTypeService;

class ClassBuilder implements ClassBuilderInterface
{
    const TYPE = 'class';
    /** @var array<string, string|array> */
    private array $parameters;

    /**
     * @param ReflectionClass $class
     * @param array<string, string|array> $parameters
     *
     * @return mixed
     * @throws ReflectionException
     * @throws Throwable
     */
    public function build(ReflectionClass $class, array $parameters): mixed
    {

        $this->parameters = $parameters;
        $constructor = $class->getConstructor();

        if($constructor instanceof ReflectionMethod){
            if(!$constructor->isPrivate()){

                try {
                    return $class->newInstanceArgs(
                        $this->handleClassWithConstructor($constructor)
                    );
                } catch (Throwable $exception) {
                    if(str_contains($exception->getMessage(), 'must be of type array, string given')){
                        throw new InvalidArgumentException(
                            sprintf('For Objects you must given an array, not an single value. Message: %s', $exception->getMessage())
                        );
                    }
                    $result = $this->handleClassWithStaticInstantiation($class);
                    if(null === $result){
                        return $this->tryExceptionSolver($class, $exception);
//                        throw $exception;
                    }

                    return $result;
                }
            }

            throw new ObjectBuilderWrongClassesGivenException(
                sprintf('Cannot handle class "%s" with private constructor.', $class->getShortName())
            );
        }

        return $this->handleClassWithoutConstructor($class);
    }

    /**
     * @throws ReflectionException
     */
    private function generateRandomValue(ReflectionParameter|ReflectionProperty $parameter): mixed
    {
        $propertyType = DataTypeService::getDataTypeFromString(
            (string)$parameter->getType()
        );

        if(null !== $propertyType){
            $propertyType = $propertyType[array_rand($propertyType)];
        }

        if(
            array_key_exists($parameter->getName(), $this->parameters) &&
            null === $this->parameters[$parameter->getName()]
        ){
            $propertyType = null;
        }

        $defaultValue = new NoValueSet();
        // todo: implement a function that enable default values
        // [$this->getDefaultValue($parameter), null, null][array_rand([0,1,2])];

        $property = new Property(
            name: $parameter->getName(),
            type: $propertyType,
            value: $this->parameters[$parameter->getName()] ?? $defaultValue,
        );

        $dataTypeHandler = DataTypeService::getDataTypeBuilder($property->type);

        if ($dataTypeHandler instanceof DataTypeInterface) {
            if (!$property->value instanceof NoValueSet) {
                $dataTypeHandler->setProperty($property);
            }

            return $dataTypeHandler->build();
        }

        try {
            return ObjectBuilder::init(
                $property->type,
                $property->value ?? []
            )->build();
        } catch (ObjectBuilderReflectionException){
            throw new ObjectBuilderDataTypeAndClassNotFoundException(
                sprintf(
                    'Property name: "%s" with value: "%s" has unknown datatype: "%s"',
                    $property->name,
                    $property->value,
                    $property->type,
                )
            );
        }
    }

    /**
     * @throws ReflectionException
     */
    private function handleClassWithoutConstructor(ReflectionClass $class): object
    {

        $object = $class->newInstance();

        foreach ($class->getProperties() as $property) {
            if ($property->getType() instanceof ReflectionType) {
                $value = $this->generateRandomValue($property);

                if ($value !== false) {
                    $property->setValue($object, $value);
                }
            }
        }

        return $object;
    }

    private function handleClassWithConstructor(ReflectionMethod $constructor): array
    {
        $parameterValues = [];

        foreach ($constructor->getParameters() as $parameter) {
            $parameterValues[] = $this->generateRandomValue($parameter);
        }

        return $parameterValues;
    }

    private function handleClassWithStaticInstantiation(ReflectionClass $class): mixed
    {
        $methods = $class->getMethods();
        foreach ($methods as $method) {
            $parameterValues = [];
            if($method->isStatic()){
                $name = $method->getName();
                foreach ($method->getParameters() as $parameter) {
                    $parameterValues[] = $this->generateRandomValue($parameter);
                }

                try {
                    return $class->getName()::$name(...$parameterValues);
                } catch (Throwable $throwable){
                    // Todo Was soll hier passieren?
                    $d = '';
                }
            }
        }

        return null;
    }

    private function getDefaultValue(ReflectionParameter|ReflectionProperty $parameter): mixed
    {
        if($parameter instanceof ReflectionParameter && $parameter->isDefaultValueAvailable()) {

            return $parameter->getDefaultValue();
        }

        if($parameter instanceof ReflectionProperty && $parameter->hasDefaultValue()) {

            return $parameter->getDefaultValue();
        }

        return null;
    }

    private function tryExceptionSolver(ReflectionClass $class, Throwable|\Exception $exception): null|object
    {
        $newParameters = [];

        if (preg_match('/Unknown or bad format \((.*)\)/', $exception->getMessage(), $unknown)) {

            $newParameters = match ($class->getName()) {
                DateInterval::class => ['P7D'],
                default => throw new UnknownOrBadFormatNotDeclaredClassException($class, $exception),
            };
            return $class->newInstanceArgs(
                $newParameters
            );
        } elseif (preg_match('/::__construct\(\) accepts (.*) as arguments/', $exception->getMessage(), $matches)) {
            // silas versuche ein $parameterOptions. Wenn er funktioniert, ok. wenn nicht, nächten match versuchen, bis keiner mehr da ist oder einer funktioniert hat.

            $parameterOptions = explode(', or ', $matches[1]);

            do{
                $key = array_rand($parameterOptions);
                $paremeters = $parameterOptions[$key];
                unset($parameterOptions[$key]);

                $result = $this->splitParametersFromExceptionMessage($paremeters);
                foreach ($result as $item) {
                    if (is_array($item)) {
                        $item = $item[array_rand($item)];
                    }

                    $dataTypeHandler = DataTypeService::getDataTypeBuilder($item);
                    $property = new Property(
                        name: null,
                        type: $item,
                        value: $dataTypeHandler?->build() ?? null, //silas value wird in 232 neu generiert. dieser wert ist überflüssig ?
                    );

                    if($dataTypeHandler instanceof DataTypeInterface){
                        $dataTypeHandler->setProperty($property);

                        $newParameters[] = $dataTypeHandler->build();
                        continue;
                    }

                    try {
                        $newParameters[] = ObjectBuilder::init(
                            $property->type,
                            $property->value ?? []
                        )->build();
                    } catch (ObjectBuilderReflectionException){
                        throw new ObjectBuilderDataTypeAndClassNotFoundException(
                            sprintf(
                                'Property name: "%s" with value: "%s" has unknown datatype: "%s"',
                                $property->name,
                                $property->value,
                                $property->type,
                            )
                        );
                    }
                }

                try {
                    return $class->newInstanceArgs(
                        $newParameters
                    );
                } catch (Throwable $exception){
                    if (preg_match('/Unknown or bad format \((.*)\)/', $exception->getMessage(), $unknown)) {

                        $newParameters = match ($class->getName()) {
                            DateInterval::class => ['P7D'],
                            DatePeriod::class => ['R4/2023-07-01T00:00:00Z/P7D'],
                            default => throw new UnknownOrBadFormatNotDeclaredClassException($class, $exception),
                        };

                        return $class->newInstanceArgs($newParameters);
                    }
                }

            } while(!empty($parameterOptions));

        }

        return $class->newInstanceArgs(
            $newParameters
        );
    }

    private function splitParametersFromExceptionMessage($function){
        $function = str_replace(' [', ', [', $function);
        $parameters = explode(', ', trim($function, '( )'));
        $result = [];
        $newArray = [];
        foreach($parameters as $parameter) {
            if($parameter[0] === '['){
                $first = substr($parameter, 1);
                $newArray[] = $first === '' ? null : $first;
                continue;
            }

            if(substr($parameter, -1) === ']'){
                $newArray[] = substr($parameter, 0, -1);
                $parameter = $newArray;
                $newArray = [];
            }
            if(!empty($newArray)){
                $newArray[] = $parameter;
            } else {
                $result[] = $parameter;
            }
        }
        return $result;
    }
}
