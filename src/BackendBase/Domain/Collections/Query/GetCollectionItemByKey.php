<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Query;

#[HandlerAttribute(GetCollectionItemByKeyHandler::class)]
class GetCollectionItemByKey
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function payload(): array
    {
        return [
            'key' => $this->key,
        ];
    }

    public function key(): string
    {
        return $this->key;
    }
}
