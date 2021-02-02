<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Command;

class DeleteCollectionItem
{
    public const COMMAND_NAME = 'collection.delete_item';
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}
