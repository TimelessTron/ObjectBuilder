<?php

declare(strict_types=1);

namespace Timelesstron\ObjectBuilder\DataTypes;

use DateTimeImmutable;
use Exception;
use Timelesstron\ObjectBuilder\ClassBuilder\Dto\NoValueSet;
use Timelesstron\ObjectBuilder\Dto\Property;

class MixedBuilder implements DataTypeInterface
{
    private ?Property $property = null;

    public function build(): mixed
    {
        if ($this->property instanceof Property && !$this->property->value instanceof NoValueSet) {

            return $this->property->value;
        }

        return $this->generateRandomValue();
    }

    public function setProperty(Property $property): self
    {
        $this->property = $property;

        return $this;
    }

    private function generateRandomValue(): mixed
    {
        $randomArray = [
            mt_rand(),
            mt_rand() / mt_getrandmax(),
            $this->generateRandomString(mt_rand(5, 15)),
            (bool)mt_rand(0, 1),
            $this->generateRandomDateTime(),
            [],
            null,
        ];

        return $randomArray[mt_rand(0, count($randomArray) - 1)];
    }

    private function generateRandomString(int $length): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     * @throws Exception
     */
    private function generateRandomDateTime(): DateTimeImmutable
    {
        return new DateTimeImmutable(
            '@' . mt_rand(1_704_067_200, time())
        );
    }

    public function buildAsString(): string
    {
        // TODO: Implement buildAsString() method.
        return var_export($this->build(), true);
    }
}
