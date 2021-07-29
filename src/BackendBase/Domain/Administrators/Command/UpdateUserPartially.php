<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Command;

use BackendBase\Domain\Shared\Exception\InvalidArgument;

use function array_keys;
use function in_array;
use function sprintf;

#[HandlerAttribute(UpdateUserPartiallyHandler::class)]
class UpdateUserPartially
{
    private array $payload            = [];
    private static array $validFields = ['first_name', 'last_name', 'email'];

    public function __construct(private string $id, array $payload)
    {
        foreach (array_keys($payload) as $fieldName) {
            if (! in_array($fieldName, self::$validFields, true)) {
                throw InvalidArgument::create(sprintf('Invalid field used to update user: %s', $fieldName));
            }
        }

        $this->payload = $payload;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
