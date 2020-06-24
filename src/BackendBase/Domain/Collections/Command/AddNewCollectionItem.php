<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Command;

class AddNewCollectionItem
{
    public const COMMAND_NAME = 'collection.create_item';
    public string $name;
    public string $key;
    public ?array $metadata;
    public ?string $parentId;

    public function __construct(string $name, string $key, ?string $parentId, ?array $metadata)
    {
        $this->name     = $name;
        $this->key      = $key;
        $this->parentId = $parentId;
        $this->metadata = $metadata;
    }

    public function payload() : array
    {
        return [
            'parentId' => $this->parentId,
            'name' => $this->name,
            'key' => $this->key,
            'metadata' => $this->metadata,
        ];
    }
}
