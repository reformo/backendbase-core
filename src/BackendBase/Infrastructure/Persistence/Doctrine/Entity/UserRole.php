<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'roles', schema: 'admin')]
class UserRole
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected string $id;

    #[ORM\Column(type: 'string')]
    protected string $title;

    #[ORM\Column(type: 'string')]
    protected string $key;

    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]

    protected ?array $permissions = [];

    #[ORM\Column(type: 'integer')]
    protected ?int $visible = 1;

    #[ORM\Column(name: 'full_permission', type: 'integer')]
    protected ?int $fullPermission = 0;

    #[ORM\Column(type: 'integer')]
    protected ?int $level = 0;

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

    public function title(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function permissions(): ?array
    {
        return $this->permissions;
    }

    public function setPermissions(?array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function visible(): ?int
    {
        return $this->visible;
    }

    public function setVisible(?int $visible): void
    {
        $this->visible = $visible;
    }

    public function fullPermission(): ?int
    {
        return $this->fullPermission;
    }

    public function setFullPermission(?int $fullPermission): void
    {
        $this->fullPermission = $fullPermission;
    }

    public function level(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): void
    {
        $this->level = $level;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
