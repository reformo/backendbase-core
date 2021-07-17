<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use DateTimeImmutable;

/**
 * @Entity
 * @Table(name="public.forms")
 */
class Forms
{
    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /** @Column(type="string",) */
    protected string $name;

    /** @Column(type="uuid", name="created_by") */
    protected string $createdBy;

    /** @Column(type="datetimetz_immutable", name="created_at") */
    protected DateTimeImmutable $createdAt;

    /** @Column(type="integer", name="is_active") */
    protected int $isActive = 1;

    /** @Column(type="json",nullable=true,options={"jsonb":true}, name="metadata") */
    protected array $metadata;

    /** @Column(type="json",nullable=true,options={"jsonb":true}, name="options") */
    protected ?array $options;

    public function id(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function createdBy(): string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function isActive(): int
    {
        return $this->isActive;
    }

    public function setIsActive(int $isActive): void
    {
        $this->isActive = $isActive;
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

    public function options(): ?array
    {
        return $this->options;
    }

    /**
     * @param array|null $options
     */
    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }
}
