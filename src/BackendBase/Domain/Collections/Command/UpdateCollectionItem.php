<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Command;

class UpdateCollectionItem
{
    public const COMMAND_NAME = 'collection.update_item';
    private array $payload;
    private string $id;

    public function __construct(string $id, array $payload)
    {
        $this->id      = $id;
        $this->payload = $payload;
    }

    public function id() : string
    {
        return $this->id;
    }

    public function payload() : array
    {
        return $this->payload;
    }
}
