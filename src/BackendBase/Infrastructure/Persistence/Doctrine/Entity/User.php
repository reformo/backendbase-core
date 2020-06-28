<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use BackendBase\Infrastructure\Persistence\Doctrine\AbstractDoctrineEntity;
use DateTimeImmutable;

/**
 * @Entity
 * @Table(name="admin.users")
 */
class User
{
    use AbstractDoctrineEntity;

    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /**
     * @Column(type="string", name="email")
     */
    protected string $email;



    /**
     * @Column(type="string", name="password_hash")
     */
    protected string $passwordHash;

    /**
     * @Column(type="string", name="password_hash_algo")
     */
    protected string $passwordHashAlgo;

    /**
     * @Column(type="string", name="first_name")
     */
    protected string $firstName;

    /**
     * @Column(type="string", name="last_name")
     */
    protected string $lastName;


    /**
     * @Column(type="integer", name="is_active")
     */
    protected int $isActive =1;
    /**
     * @Column(type="integer", name="is_deleted")
     */
    protected int $isDeleted =0;

    /**
     * @Column(type="string", name="role")
     */
    protected string $role = '';

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

    public function getFullName() : string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
