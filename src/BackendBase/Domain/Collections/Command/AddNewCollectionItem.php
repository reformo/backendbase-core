<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Command;

#[HandlerAttribute(AddNewCollectionItemHandler::class)]
class AddNewCollectionItem
{
    public const COMMAND_NAME = 'collection.create_item';

    public function __construct(public string $name, public string $key, public ?string $parentId, public ?array $metadata)
    {
    }

    public function payload(): array
    {
        return [
            'parentId' => $this->parentId,
            'name' => $this->name,
            'key' => $this->key,
            'metadata' => $this->metadata,
        ];
    }
}
