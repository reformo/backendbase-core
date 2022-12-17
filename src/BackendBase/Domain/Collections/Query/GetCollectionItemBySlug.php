<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Query;

#[HandlerAttribute(GetCollectionItemBySlugHandler::class)]
class GetCollectionItemBySlug
{
    public function __construct(private ?string $parentId, private string $slug)
    {
    }

    public function payload(): array
    {
        return [
            'parentId' => $this->parentId,
            'slug' => $this->slug,
        ];
    }

    public function parentId(): ?string
    {
        return $this->parentId;
    }

    public function slug(): string
    {
        return $this->slug;
    }
}
