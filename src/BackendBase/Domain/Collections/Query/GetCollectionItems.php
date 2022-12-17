<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Query;
use BackendBase\Shared\CQRS\HandlerAttribute;

#[HandlerAttribute(GetCollectionItemsHandler::class)]
class GetCollectionItems
{
    public function __construct(private ?string $parentId, private ?int $offset = 0, private ?int $limit = 25)
    {
    }

    public function payload(): array
    {
        return [
            'parentId' => $this->parentId,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];
    }

    public function parentId(): ?string
    {
        return $this->parentId;
    }

    public function offset(): int
    {
        return $this->offset;
    }

    public function limit(): int
    {
        return $this->limit;
    }
}
