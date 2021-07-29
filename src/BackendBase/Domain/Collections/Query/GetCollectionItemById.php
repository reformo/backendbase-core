<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Query;

#[HandlerAttribute(GetCollectionItemByIdHandler::class)]
class GetCollectionItemById
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function payload(): array
    {
        return [
            'id' => $this->id,
        ];
    }

    public function id(): string
    {
        return $this->id;
    }
}
