<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine;

use InvalidArgumentException;

use function array_key_exists;
use function get_class_vars;
use function get_object_vars;
use function in_array;
use function lcfirst;
use function sprintf;
use function str_replace;
use function strpos;

use const DATE_ATOM;

trait AbstractDoctrineEntity
{
    private array $fields     = [];
    private bool $isFieldsSet = false;

    public function setFields(): void
    {
        $fields = get_class_vars(static::class);
        foreach ($fields as $field => $value) {
            if ($field === 'fields') {
                continue;
            }

            if ($field === 'areFieldsSet') {
                continue;
            }

            $this->fields[] = $field;
        }

        $this->isFieldsSet = true;
    }

    public function __call($name, $arguments)
    {
        if ($this->isFieldsSet === false) {
            $this->setFields();
        }

        if (strpos($name, 'set') === 0) {
            $fieldName =  lcfirst(str_replace('set', '', $name)); //CaseConverter::toSnakeCase(str_replace('set', '', $name));
            if (! in_array($fieldName, $this->fields, true)) {
                throw new InvalidArgumentException(sprintf('Invalid set field function %s', $fieldName));
            }

            $this->{$fieldName} = $arguments[0];

            return true;
        }

        //$fieldName = lcfirst($);// CaseConverter::toSnakeCase($name);
        if (! in_array($name, $this->fields, true)) {
            throw new InvalidArgumentException(sprintf('Invalid get field function %s', $name));
        }

        return $this->{$name};
    }

    public function toArray(): array
    {
        $dateFields = ['createdAt', 'updatedAt', 'expireAt', 'publishAt'];
        $values     = get_object_vars($this);
        unset($values['fields'], $values['isFieldsSet']);
        foreach ($dateFields as $dateField) {
            if (! array_key_exists($dateField, $values)) {
                continue;
            }

            $values[$dateField] = $values[$dateField]->format(DATE_ATOM);
        }

        return $values;
    }
}
