<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Command;

#[HandlerAttribute(UpdateCollectionItemHandler::class)]
class UpdateCollectionItem
{
    public const COMMAND_NAME = 'collection.update_item';

    public function __construct(private string $id, private array $payload)
    {
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
