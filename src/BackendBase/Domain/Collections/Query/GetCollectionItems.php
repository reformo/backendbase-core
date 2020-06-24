<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Query;

class GetCollectionItems
{
    private ?string $parentId;
    private int $limit;
    private int $offset;

    public function __construct(?string $parentId, ?int $offset = 0, ?int $limit = 25)
    {
        $this->parentId = $parentId;
        $this->offset   = $offset;
        $this->limit    = $limit;
    }

    public function payload() : array
    {
        return [
            'parentId' => $this->parentId,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];
    }

    public function parentId() : ?string
    {
        return $this->parentId;
    }

    public function offset() : int
    {
        return $this->offset;
    }

    public function limit() : int
    {
        return $this->limit;
    }
}
