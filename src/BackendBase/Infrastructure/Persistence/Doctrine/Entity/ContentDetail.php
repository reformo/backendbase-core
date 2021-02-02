<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use BackendBase\Infrastructure\Persistence\Doctrine\AbstractDoctrineEntity;

/**
 * @Entity
 * @Table(name="public.content_details")
 */
class ContentDetail
{
    use AbstractDoctrineEntity;

    /**
     * @Id
     * @Column(type="uuid")
     * @GeneratedValue(strategy="NONE")
     */
    protected string $id;

    /** @Column(type="string", name="content_id") */
    protected string $contentId;

    /** @Column(type="string", name="language") */
    protected string $language;


    /** @Column(type="string", name="region") */
    protected string $region;


    /** @Column(type="string", name="title") */
    protected string $title;

    /** @Column(type="string", name="slug") */
    protected string $slug;


    /** @Column(type="string", name="serp_title") */
    protected string $serpTitle;

    /** @Column(type="string", name="description") */
    protected ?string $description;


    /** @Column(type="string") */
    protected ?string $keywords;

    /** @Column(type="json",nullable=true,options={"jsonb":true}) */
    protected ?array $body = [];

    /** @Column(type="string", name="body_fulltext") */
    protected ?string $bodyFulltext = '';

    /** @Column(type="integer", name="is_active") */
    protected int $isActive;

    public function id(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function contentId(): string
    {
        return $this->contentId;
    }

    public function setContentId(string $contentId): void
    {
        $this->contentId = $contentId;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    public function region(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function keywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): void
    {
        $this->keywords = $keywords;
    }

    public function body(): ?array
    {
        return $this->body;
    }

    /**
     * @param array|null $body
     */
    public function setBody(?array $body): void
    {
        $this->body = $body;
    }

    public function bodyFulltext(): ?string
    {
        return $this->bodyFulltext;
    }

    public function setBodyFulltext(?string $bodyFulltext): void
    {
        $this->bodyFulltext = $bodyFulltext;
    }

    public function isActive(): int
    {
        return $this->isActive;
    }

    public function setIsActive(int $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function serpTitle(): string
    {
        return $this->serpTitle;
    }

    public function setSerpTitle(string $serpTitle): void
    {
        $this->serpTitle = $serpTitle;
    }
}
