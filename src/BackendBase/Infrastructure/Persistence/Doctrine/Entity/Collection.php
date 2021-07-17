<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

/**
 * @Entity
 * @Table(name="public.lookup_table")
 */
class Collection
{
    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /** @Column(type="string") */
    protected string $key;

    /** @Column(type="string") */
    protected string $name;

    /** @Column(type="string") */
    protected string $slug;

    /** @Column(type="uuid",nullable=true, name="parent_id") */
    protected ?string $parentId;

    /** @Column(type="json_array",nullable=true,options={"jsonb"=true}) */
    protected array $metadata;

    /** @Column(type="integer",name="is_active") */
    protected int $isActive;

    /** @Column(type="integer",name="is_deleted",options={"default":0}) */
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

    /**
     * @param array $metadata
     */
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
