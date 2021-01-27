<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services\Persistence;

use BackendBase\Domain\Shared\Exception\InvalidArgument;
use Selami\Stdlib\CaseConverter;

use function get_object_vars;
use function method_exists;
use function property_exists;
use function sprintf;
use function ucfirst;

trait ResultObject
{
    public function __set(string $name, $value): void
    {
        $propertyName          = CaseConverter::toCamelCase($name);
        $transformFunctionName = 'transform' . ucfirst($propertyName);
        if (! property_exists($this, $propertyName)) {
            throw InvalidArgument::create(
                sprintf(
                    'FetchCustomObject does not have property named: %s (%s).',
                    $propertyName,
                    $name
                )
            );
        }

        if (method_exists(static::class, $transformFunctionName)) {
            $value = $this->{$transformFunctionName}($value);
        }

        $this->{$propertyName} = $value;
    }

    public function __isset($name)
    {
        return property_exists($this, $name);
    }

    public function __get($name)
    {
        if (! property_exists($this, $name)) {
            throw InvalidArgument::create(
                sprintf(
                    'This object does not have a property named: (%s)',
                    $name
                )
            );
        }

        return $this->{$name};
    }

    public function __call($name, $params)
    {
        if (! property_exists($this, $name)) {
            throw InvalidArgument::create(
                sprintf(
                    'This object does not have a property named: (%s)',
                    $name
                )
            );
        }

        return $this->{$name};
    }

    public function toArray(): array
    {
        $objectVars =  get_object_vars($this);

        $propertiesAsAnArray = [];
        foreach ($objectVars as $key => $value) {
            $propertiesAsAnArray[$key] = $this->{$key}();
        }

        return $propertiesAsAnArray;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
