<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use BackendBase\Infrastructure\Persistence\Doctrine\AbstractDoctrineEntity;
use DateTimeImmutable;

/**
 * @Entity
 * @Table(name="public.files")
 * @method Collection getId() : string
 * @method Collection setId(string $id) : void
 * @method Collection getFilePath() : string
 * @method Collection setFilePath(string $filePath) : void
 * @method Collection getType() : string
 * @method Collection setType(string $type) : void
 * @method Collection getUploadedAt() : string
 * @method Collection getMetadata() : array
 * @method Collection setMetadata(array $metaData) : void
 */
class File
{
    use AbstractDoctrineEntity;

    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /**
     * @Column(type="string", name="file_path")
     */
    protected string $filePath;

    /**
     * @Column(type="string")
     */
    protected string $type;

    /**
     * @Column(type="json",nullable=true,options={"jsonb":true})
     */
    protected array $metadata;

    /**
     * @Column(type="datetimetz_immutable", name="uploaded_at")
     */
    protected DateTimeImmutable $uploadedAt;

    public function setUploadedAt(DateTimeImmutable $datetime) : void
    {
        $this->uploadedAt = $datetime;
    }

    public function __construct()
    {
        $this->setFields();
    }
}
