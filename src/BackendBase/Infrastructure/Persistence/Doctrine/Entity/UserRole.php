<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use DateTimeImmutable;

/**
 * @Entity
 * @Table(name="admin.roles")
 */
class UserRole
{
    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /** @Column(type="string") */
    protected string $title;

    /** @Column(type="string") */
    protected string $key;

    /** @Column(type="json",nullable=true,options={"jsonb":true}, name="permissions") */
    protected ?array $permissions = [];

    /** @Column(type="integer", name="visible") */
    protected ?int $visible = 1;

    /** @Column(type="integer", name="full_permission") */
    protected ?int $fullPermission = 0;

    /** @Column(type="integer") */
    protected ?int $level = 0;

    /** @Column(type="datetimetz_immutable", name="created_at") */
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

    /**
     * @param array|null $permissions
     */
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
