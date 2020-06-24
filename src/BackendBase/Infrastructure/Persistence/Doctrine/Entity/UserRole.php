<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use DateTimeImmutable;
use BackendBase\Infrastructure\Persistence\Doctrine\AbstractDoctrineEntity;

/**
 * @Entity
 * @Table(name="admin.roles")
 */
class UserRole
{
    use AbstractDoctrineEntity;

    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /**
     * @Column(type="string")
     */
    protected string $title;

    /**
     * @Column(type="string")
     */
    protected string $key;

    /**
     * @Column(type="json",nullable=true,options={"jsonb":true}, name="permissions")
     */
    protected ? array $permissions = [];
    /**
     * @Column(type="integer", name="visible")
     */
    protected ?int $visible =1;

    /**
     * @Column(type="integer", name="full_permission")
     */
    protected ?int $fullPermission = 0;
    /**
     * @Column(type="integer")
     */
    protected ?int $level = 0;

    /**
     * @Column(type="datetimetz_immutable", name="created_at")
     */

    protected DateTimeImmutable $createdAt;

    public function setCreatedAt(DateTimeImmutable $datetime) : void
    {
        $this->createdAt = $datetime;
    }

    public function __construct()
    {
        $this->setFields();
    }
}
