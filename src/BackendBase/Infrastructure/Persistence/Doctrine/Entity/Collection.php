<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use BackendBase\Infrastructure\Persistence\Doctrine\AbstractDoctrineEntity;

/**
 * @Entity
 * @Table(name="public.lookup_table")
 * @method Collection getId() : string
 * @method Collection setId(string $id) : void
 * @method Collection getKey() : string
 * @method Collection setKey(string $key) : void
 * @method Collection getName() : string
 * @method Collection setName(string $name) : void
 * @method Collection getSlug() : string
 * @method Collection setSlug(string $slug) : void
 * @method Collection getParentId() : string
 * @method Collection setParentId(string $parentId) : void
 * @method Collection getMetadata() : array
 * @method Collection setMetadata(array $metaData) : void
 * @method Collection getIsActive() : int
 * @method Collection setIsActive(int $isActive) : void
 * @method Collection getIsDeleted() : int
 * @method Collection setIsDeleted(int $isDeleted) : void
 */
class Collection
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
    protected string $key;

    /**
     * @Column(type="string")
     */
    protected string $name;

    /**
     * @Column(type="string")
     */
    protected string $slug;

    /**
     * @Column(type="uuid",nullable=true, name="parent_id")
     */
    protected ?string $parentId;

    /**
     * @Column(type="json_array",nullable=true,options={"jsonb"=true})
     */
    protected array $metadata;

    /**
     * @Column(type="integer",name="is_active")
     */
    protected int $isActive;

    /**
     * @Column(type="integer",name="is_deleted",options={"default":0})
     */
    protected int $isDeleted;

    public function __construct()
    {
        $this->setFields();
    }
}
