<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Command;

#[HandlerAttribute(DeleteCollectionItemHandler::class)]
class DeleteCollectionItem
{
    public const COMMAND_NAME = 'collection.delete_item';

    public function __construct(private string $id)
    {
    }

    public function id(): string
    {
        return $this->id;
    }
}
