<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use BackendBase\Infrastructure\Persistence\Doctrine\AbstractDoctrineEntity;
use DateTimeImmutable;

/**
 * @Entity
 * @Table(name="public.form_data")
 */
class FormData
{
    use AbstractDoctrineEntity;

    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /**
     * @Column(type="uuid", name="form_id")
     */
    protected string $formId;

    /**
     * @Column(type="json",nullable=true,options={"jsonb":true}, name="post_data")
     */
    protected array $postData;

    /**
     * @Column(type="string", name="client_ip")
     */
    protected string $clientIp;

    /**
     * @Column(type="integer", name="is_moderated")
     */
    protected int $isModerated =1;

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
