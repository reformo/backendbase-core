<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'lookup_table', schema: 'public')]
class Collection
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected string $id;

    #[ORM\Column(type: 'string')]
    protected string $key;

    #[ORM\Column(type: 'string')]
    protected string $name;

    #[ORM\Column(type: 'string')]
    protected string $slug;

    #[ORM\Column(name: 'parent_id', type: 'uuid', nullable: true)]
    protected ?string $parentId = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    protected array $metadata;

    #[ORM\Column(name: 'is_active', type: 'integer')]
    protected int $isActive;

    #[ORM\Column(name: 'is_deleted', type: 'integer', options: ['default' => 0])]

    protected int $isDeleted;

    public function id(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function parentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function isActive(): int
    {
        return $this->isActive;
    }

    public function setIsActive(int $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function isDeleted(): int
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(int $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }
}
