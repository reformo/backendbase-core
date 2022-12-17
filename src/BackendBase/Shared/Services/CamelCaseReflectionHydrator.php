<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Laminas\Hydrator\AbstractHydrator;
use ReflectionClass;
use ReflectionProperty;
use Selami\Stdlib\CaseConverter;

use function get_class;
use function in_array;

use const DATE_ATOM;

class CamelCaseReflectionHydrator extends AbstractHydrator
{
    /**
     * Simple in-memory array cache of ReflectionProperties used.
     *
     * @var ReflectionProperty[][]
     */
    protected static array $reflProperties = [];

    /**
     * Extract values from an object
     *
     * {@inheritDoc}
     */
    public function extract(object $object): array
    {
        $result              = [];
        $unsetPropertyBucket = $object->getUnsetPropertyBucket();
        foreach (self::getReflProperties($object) as $property) {
            $propertyName = $this->extractName($property->getName(), $object);
            if (in_array($propertyName, $unsetPropertyBucket, true)) {
                continue;
            }

            if (! $this->getCompositeFilter()->filter($propertyName)) {
                continue;
            }

            $value                 = $property->getType()->getName() === DateTimeImmutable::class ?
                $property->getValue($object)->format(DATE_ATOM) : $property->getValue($object);
            $result[$propertyName] = $this->extractValue($propertyName, $value, $object);
        }

        return $result;
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * {@inheritDoc}
     */
    public function hydrate(array $data, object $object): object
    {
        $reflProperties = self::getReflProperties($object);
        foreach ($data as $key => $value) {
            $key  = CaseConverter::toCamelCase($key);
            $name = $this->hydrateName($key, $data);
            if (! isset($reflProperties[$name])) {
                continue;
            }

            $reflProperties[$name]->setValue(
                $object,
                $this->hydrateValue($name, $this->getValueByType($reflProperties[$name], $value), $data)
            );
        }

        return $object;
    }

    public function getValueByType(ReflectionProperty $property, $value): mixed
    {
        $propertyType                 = $property->getType()?->getName();
        $specialTypes                 = [
            'DateTimeImmutable' => DateTimeImmutable::class,
        ];
        $specialTypeDateTimeImmutable = $specialTypes['DateTimeImmutable'];

        return match ($propertyType) {
            $specialTypeDateTimeImmutable => (new CarbonImmutable($value))->toDateTimeImmutable(),
            default => $value
            // phpcs:disable
        };
        // phpcs:enable
    }

    /**
     * Get a reflection properties from in-memory cache and lazy-load if
     * class has not been loaded.
     *
     * @return ReflectionProperty[]
     */
    protected static function getReflProperties(object $input): array
    {
        $class = $input::class;

        if (isset(static::$reflProperties[$class])) {
            return static::$reflProperties[$class];
        }

        static::$reflProperties[$class] = [];
        $reflClass                      = new ReflectionClass($class);
        $reflProperties                 = $reflClass->getProperties();

        foreach ($reflProperties as $property) {
            $property->setAccessible(true);
            static::$reflProperties[$class][$property->getName()] = $property;
        }

        return static::$reflProperties[$class];
    }
}
