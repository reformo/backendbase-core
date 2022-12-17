<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'content_details', schema: 'public')]
class ContentDetail
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected string $id;

    #[ORM\Column(name: 'content_id', type: 'uuid')]
    protected string $contentId;

    #[ORM\Column(type: 'string')]
    protected string $language;

    #[ORM\Column(type: 'string')]
    protected string $region;

    #[ORM\Column(type: 'string')]
    protected string $title;

    #[ORM\Column(type: 'string')]
    protected string $slug;

    #[ORM\Column(name: 'serp_title', type: 'string')]
    protected string $serpTitle;

    #[ORM\Column(type: 'string')]
    protected ?string $description = null;

    #[ORM\Column(type: 'string')]
    protected ?string $keywords = null;

    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    protected ?array $body = [];

    #[ORM\Column(name: 'body_fulltext', type: 'string')]
    protected ?string $bodyFulltext = '';

    #[ORM\Column(name: 'is_active', type: 'integer')]
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
