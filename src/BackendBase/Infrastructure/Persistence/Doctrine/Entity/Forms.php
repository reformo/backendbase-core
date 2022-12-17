<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'forms', schema: 'public')]
class Forms
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected string $id;

    #[ORM\Column(type: 'string')]
    protected string $name;

    #[ORM\Column(name: 'created_by', type: 'uuid')]
    protected string $createdBy;

    #[ORM\Column(name: 'is_active', type: 'integer')]
    protected int $isActive = 1;

    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    protected array $metadata;

    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    protected ?array $options;

    #[ORM\Column(name: 'created_at', type: 'datetimetz_immutable')]
    protected DateTimeImmutable $createdAt;

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

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function options(): ?array
    {
        return $this->options;
    }

    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }
}
