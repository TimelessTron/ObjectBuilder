<?php

namespace Timelesstron\ObjectBuilder\ClassBuilder\Interface;

use ReflectionClass;
use Timelesstron\ObjectBuilder\ClassBuilder\Dto\NoValueSet;
use Timelesstron\ObjectBuilder\ClassBuilder\InterfaceBuilder;
use Timelesstron\ObjectBuilder\Dto\Property;
use Timelesstron\ObjectBuilder\Exceptions\InfinityInterfaceException;
use Timelesstron\ObjectBuilder\ObjectBuilder;
use Timelesstron\ObjectBuilder\Services\DataTypeService;

final class FileContentHandler implements HandlerInterface
{

    const HAS_NO_VALUE = true;
    private ReflectionClass $reflectionClass;
    private array $parameters;

    private string $className;
    private string $namespace;

    /** @var array<int, string> $useStatements */
    private array $useStatements = [];

    public function execute(ReflectionClass $reflectionClass, array $parameters): object
    {
        $this->init($reflectionClass, $parameters);

        $rowsOfFileContent = file(
            $reflectionClass->getFileName(),
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES,
        );

        $x = $this->handleRowOfFileContent($rowsOfFileContent);

        eval($x);

        return new $this->className();
    }

    public static function support(ReflectionClass $reflectionClass): bool
    {
        if (!$reflectionClass->getFileName()){
            return false;
        }

        return !empty(
            file(
                $reflectionClass->getFileName(),
                FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES,
            )
        );
    }

    private function handleRowOfFileContent(array $rowsOfFileContent): string
    {
        $newRowsOfContent = [];
        foreach ($rowsOfFileContent as $contentRow) {
            if ('<?php' === $contentRow) {
                continue;
            }

            if (str_contains($contentRow, 'namespace')) {
//                $newRowsOfContent[] = $this->addNamespaceToContent($contentRow);
                $this->addNamespaceToContent($contentRow);
                continue;
            }

            if ($this->isUseStatement($contentRow)) {
                $this->collectUseStatements($contentRow);
                continue;
            }

            $newRowsOfContent[] = match (true) {
                $this->isInterfaceName($contentRow) =>
                $this->buildClassNameString($contentRow),
                $this->isConstruct($contentRow) => $this->buildConstructString($contentRow),
                $this->isMethode($contentRow) => $this->buildMethodeString($contentRow),
                default => $contentRow
            };
        }

        return implode("\n", [...$this->useStatements,'',...$newRowsOfContent]);
    }

    private function buildMethodeString(string $contentRow): string
    {
        $hasReturnValue = preg_match('/:\s*((?:\w+\|?)+).*/', $contentRow, $returnValue);
        if (!$hasReturnValue) {
            return $this->methodeWithoutReturnValue($contentRow);
        }

        $returnType = $this->getReturnType($returnValue[1]);
        $methodeName = $this->getMethodeName($contentRow);

        $property = new Property(
            name: $methodeName,
            type: $returnType,
            value: $this->parameters[$methodeName] ?? new NoValueSet(),
        );

        $dataTypeBuilder = DataTypeService::getDataTypeBuilder($returnType);

        // gibt es contentRow return type als use statement? wenn nicht, Hinzufügen
        if (null === $dataTypeBuilder) {
            if (!isset($this->useStatements[$returnType]) && (bool)$this->namespace){
                $this->useStatements[$returnType] = sprintf('use %s\%s;', $this->namespace, $returnType);
            }
        }

        $dataTypeBuilderString = match(true){
            null === $dataTypeBuilder && !$property->value instanceof NoValueSet =>
                $this->methodeWithGivenReturnObject($returnType, $property->value),
            null === $dataTypeBuilder && $property->value instanceof NoValueSet => $this->methodeWithObjectAsReturnValue($returnType),
            !$property->value instanceof NoValueSet => $dataTypeBuilder->setProperty($property)->buildAsString(),
            default => $dataTypeBuilder->buildAsString()
        };

        return sprintf(
            '%s { return %s; }',
            trim($contentRow, ';'),
            $dataTypeBuilderString,
        );
    }

    private function init(ReflectionClass $reflectionClass, array $parameters): void
    {
        $this->reflectionClass = $reflectionClass;
        $this->parameters = $parameters;
        $this->className = $this->increaseClassNameIfNeeded();
    }

    private function increaseClassNameIfNeeded(): string
    {
        $className = sprintf('%sClass', $this->reflectionClass->getShortName());

        while (true) {
            if (!class_exists($className)) {

                return $className;
            }

            $className = preg_replace_callback(
                '/(\D*)(\d+)?/',
                fn($matches): string => $matches[1] . intval($matches[2] ?? 0) + 1,
                $className,
                1
            );
        }
    }

    private function addNamespaceToContent(string $contentRow): string
    {
        $this->namespace = trim(str_replace('namespace', '', trim($contentRow, ';')));
        return str_replace('namespace', 'use', $contentRow);
    }

    private function isUseStatement(string $contentRow): bool
    {
        return preg_match(
            '/^use\s+([a-zA-Z\\\\]+(?:\s*,\s*[a-zA-Z\\\\]+)*)\s*;$/',
            $contentRow
        );
    }

    private function collectUseStatements(string $contentRow): void
    {
        $partsOfNamespace = explode('\\', $contentRow);
        $className = trim(end($partsOfNamespace), ';');
        $this->useStatements[$className] = implode('\\', $partsOfNamespace);
    }

    private function isInterfaceName(string $contentRow): bool
    {
        return preg_match(
            sprintf('/^interface %s/', $this->reflectionClass->getShortName()),
            $contentRow,
        );
    }

    private function buildClassNameString(string $contentRow): string
    {
        return strtr(
            $contentRow,
            [
                $this->reflectionClass->getShortName() => $this->reflectionClass->getName(),
                'interface ' => sprintf('class %s implements ', $this->className),
            ],
        );
    }

    private function isConstruct(string $contentRow): bool
    {
        return preg_match(
            '/.*__construct.*;$/',
            $contentRow
        );
    }

    private function isMethode(string $contentRow): bool
    {
        return preg_match(
            '/^.*function.*;$/',
            $contentRow
        );
    }

    private function buildConstructString(string $contentRow): string
    {
        return sprintf('%s {}', trim($contentRow, ';'));
    }

    private function methodeWithoutReturnValue(string $contentRow): string
    {
        return sprintf(
            '%s {  }',
            trim($contentRow, ';'),
        );
    }

    /**
     * @param $dataType
     *
     * @return void
     */
    public function getReturnType($dataType): string
    {
        $returnTypes = DataTypeService::getDataTypeFromString($dataType);
        return $returnTypes[array_rand($returnTypes)];
    }

    private function getMethodeName(string $contentRow): string
    {
        preg_match('/function (.*)\(/', $contentRow, $methodeName);
        return trim($methodeName[1]);
    }

    private function methodeWithGivenReturnObject(string $type, mixed $value): string
    {
        if (is_object($value)) {
            return sprintf(
                'unserialize(\'%s\')',
                serialize($value)
            );
        }

        return sprintf(
            '%s::init(%s::class, %s)->build()',
            ObjectBuilder::class,
            $type,
            var_export($value, true),
        );
    }

    private function methodeWithObjectAsReturnValue(?string $returnType): string
    {
        if (str_contains($this->className, $returnType . 'Class')) {
            // Create namespace for class in the same namespace.
            $returnTypeWithNamespace = sprintf('%s\\%s', trim($this->namespace), $returnType);

            if(InterfaceBuilder::counter() > InterfaceBuilder::MAX_ALLOWED_INFINITY_INTERFACE_LOADER){
                throw new InfinityInterfaceException();
            }
            // todo Test testAndReturnValues in InterfaceBuilderTest geht nicht. hier gibt es probleme.
            return sprintf(
                'unserialize(\'%s\')',
                serialize(
                    ObjectBuilder::init($returnTypeWithNamespace)->build()
                )
            );

        }

        return sprintf(
            '%s::init(%s::class)->build()',
            ObjectBuilder::class,
            $returnType,
        );
    }
}
