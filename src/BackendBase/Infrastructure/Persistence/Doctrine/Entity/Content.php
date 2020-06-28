<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use BackendBase\Infrastructure\Persistence\Doctrine\AbstractDoctrineEntity;
use DateTimeImmutable;
use function base_convert;
use function hrtime;

/**
 * @Entity
 * @Table(name="public.contents")
 */
class Content
{
    use AbstractDoctrineEntity;

    public const CONTENT_IS_ACTIVE         = 1;
    public const CONTENT_IS_PASSIVE        = 0;
    public const CONTENT_IS_NOT_ACCESSIBLE = 1;
    public const CONTENT_IS_ACCESSIBLE     = 0;

    public const CONTENT_TYPE_FULL            = 'full';
    public const CONTENT_TYPE_SIMPLE          = 'simple';
    public const CONTENT_TYPE_IMAGE_LINK      = 'image-link';
    public const CONTENT_TYPE_KEY_VALUE       = 'key-value';
    public const CONTENT_TYPE_IMAGE_KEY_VALUE = 'image-key-value';

    public const WITH_CONTENT_BODY = true;



    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /**
     * @Column(type="string", name="title")
     */
    protected string $title;


    /**
     * @Column(type="string", name="serp_title")
     */
    protected ?string $serpTitle;

    /**
     * @Column(type="string")
     */
    protected string $type;

    /**
     * @Column(type="string")
     */
    protected string $category;


    /**
     * @Column(type="string", name="meta_description")
     */
    protected ?string $metaDescription;



    /**
     * @Column(type="string", name="serp_meta_description")
     */
    protected ?string $serpMetaDescription;

    /**
     * @Column(type="string")
     */
    protected ?string $keywords;

    /**
     * @Column(type="string")
     */
    protected ?string $robots;

    /**
     * @Column(type="json",nullable=true,options={"jsonb":true})
     */
    protected ?array $canonical;

    /**
     * @Column(type="json",nullable=true,options={"jsonb":true})
     */
    protected ?array $metadata = [];
    /**
     * @Column(type="string")
     */
    protected ?string $redirect;


    /**
     * @Column(type="string")
     */
    protected ? string $body = '';


    /**
     * @Column(type="json",nullable=true,options={"jsonb":true})
     */
    protected ?array $images = [];


    /**
     * @Column(type="string", name="sort_order")
     */
    protected string $sortOrder;


    /**
     * @Column(type="integer", name="is_active")
     */
    protected int $isActive;


    /**
     * @Column(type="integer", name="is_deleted")
     */
    protected int $isDeleted;


    /**
     * @Column(type="datetimetz_immutable", name="created_at")
     */
    protected DateTimeImmutable $createdAt;

    public function setCreatedAt(DateTimeImmutable $datetime) : void
    {
        $this->createdAt = $datetime;
    }

    /**
     * @Column(type="uuid", name="created_by")
     */
    protected string $createdBy;
    /**
     * @Column(type="datetimetz_immutable", name="updated_at")
     */
    protected DateTimeImmutable $updatedAt;

    public function setUpdatedAt(DateTimeImmutable $datetime) : void
    {
        $this->updatedAt = $datetime;
    }

    /**
     * @Column(type="uuid", name="updated_by")
     */
    protected string $updatedBy;

    public static function generateSortValue() : string
    {
        return base_convert(hrtime(true), 10, 16);
    }
}
