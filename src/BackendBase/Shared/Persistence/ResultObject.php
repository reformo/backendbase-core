<?php
declare(strict_types=1);

namespace BackendBase\Shared\Persistence;

use BackendBase\Shared\Services\CamelCaseReflectionHydrator;

trait ResultObject
{
    private array $unsetPropertyBucket = ['unsetPropertyBucket'];

    public function unset(...$properties) : void
    {
        foreach ($properties as $property) {
            $this->unsetPropertyBucket[] = $property;
        }
    }
    public function getUnsetPropertyBucket() : array
    {
        return $this->unsetPropertyBucket;
    }
    public function toArray(): array
    {
        return(new CamelCaseReflectionHydrator())->extract($this);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
