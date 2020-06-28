<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use BackendBase\Infrastructure\Persistence\Doctrine\AbstractDoctrineEntity;
use DateTimeImmutable;

/**
 * @Entity
 * @Table(name="public.forms")
 */
class Forms
{
    use AbstractDoctrineEntity;

    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /**
     * @Column(type="string",)
     */
    protected string $name;

    /**
     * @Column(type="uuid", name="created_by")
     */
    protected string $createdBy;

    /**
     * @Column(type="json",nullable=true,options={"jsonb":true}, name="metadata")
     */
    protected array $metadata;

    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    public function setCreatedBy(string $createdBy) : void
    {
        $this->createdBy = $createdBy;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata) : void
    {
        $this->metadata = $metadata;
    }

    public function setIsActive(int $isActive) : void
    {
        $this->isActive = $isActive;
    }

    /**
     * @Column(type="integer", name="is_active")
     */
    protected int $isActive =1;

    /**
     * @Column(type="datetimetz_immutable", name="created_at")
     */

    protected DateTimeImmutable $createdAt;

    public function setCreatedAt(DateTimeImmutable $datetime) : void
    {
        $this->createdAt = $datetime;
    }

    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    public function setClientIp(string $clientIp) : void
    {
        $this->clientIp = $clientIp;
    }

    public function setFormId(string $formId) : void
    {
        $this->formId = $formId;
    }

    /**
     * @param array $postData
     */
    public function setPostData(array $postData) : void
    {
        $this->postData = $postData;
    }

    public function setIsModerated(int $isModerated) : void
    {
        $this->isModerated = $isModerated;
    }
}
