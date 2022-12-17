<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

use function base_convert;
use function hrtime;

#[ORM\Entity]
#[ORM\Table(name: 'contents', schema: 'public')]
class Content
{
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

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected string $id;

    #[ORM\Column(type: 'string')]
    protected string $category;

    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    protected array $tags;

    #[ORM\Column(type: 'string')]
    protected string $template;

    #[ORM\Column(type: 'string')]
    protected string $robots;

    #[ORM\Column(name: 'redirect_url', type: 'string')]
    protected ?string $redirectUrl = null;

    #[ORM\Column(name: 'cover_image_landscape', type: 'string')]
    protected ?string $coverImageLandscape = null;

    #[ORM\Column(name: 'cover_image_portrait', type: 'string')]
    protected ?string $coverImagePortrait = null;

    #[ORM\Column(name: 'sort_order', type: 'string')]
    protected string $sortOrder;

    #[ORM\Column(name: 'is_active', type: 'integer')]
    protected int $isActive;

    #[ORM\Column(name: 'is_deleted', type: 'integer')]
    protected int $isDeleted;

    public function id(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function template(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function robots(): string
    {
        return $this->robots;
    }

    public function setRobots(string $robots): void
    {
        $this->robots = $robots;
    }

    public function redirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(?string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    public function coverImageLandscape(): ?string
    {
        return $this->coverImageLandscape;
    }

    public function setCoverImageLandscape(?string $coverImageLandscape): void
    {
        $this->coverImageLandscape = $coverImageLandscape;
    }

    public function coverImagePortrait(): ?string
    {
        return $this->coverImagePortrait;
    }

    public function setCoverImagePortrait(?string $coverImagePortrait): void
    {
        $this->coverImagePortrait = $coverImagePortrait;
    }

    public function sortOrder(): string
    {
        return $this->sortOrder;
    }

    public function setSortOrder(string $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    public function isActive(): int
    {
        return $this->isActive;
    }

    public function setIsActive(int $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function isDeleted(): int
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(int $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function createdBy(): string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function updatedBy(): string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(string $updatedBy): void
    {
        $this->updatedBy = $updatedBy;
    }

    public function publishAt(): DateTimeImmutable
    {
        return $this->publishAt;
    }

    public function setPublishAt(DateTimeImmutable $publishAt): void
    {
        $this->publishAt = $publishAt;
    }

    public function expireAt(): ?DateTimeImmutable
    {
        return $this->expireAt;
    }

    public function setExpireAt(?DateTimeImmutable $expireAt): void
    {
        $this->expireAt = $expireAt;
    }

    /** @Column(type="datetimetz_immutable", name="created_at") */
    protected DateTimeImmutable $createdAt;

    public function setCreatedAt(DateTimeImmutable $datetime): void
    {
        $this->createdAt = $datetime;
    }

    /** @Column(type="uuid", name="created_by") */
    protected string $createdBy;
    /** @Column(type="datetimetz_immutable", name="updated_at") */
    protected DateTimeImmutable $updatedAt;

    public function setUpdatedAt(DateTimeImmutable $datetime): void
    {
        $this->updatedAt = $datetime;
    }

    /** @Column(type="uuid", name="updated_by") */
    protected string $updatedBy;


    /** @Column(type="datetimetz_immutable", name="publish_at") */
    protected DateTimeImmutable $publishAt;


    /** @Column(type="datetimetz_immutable", name="expire_at") */
    protected ?DateTimeImmutable $expireAt = null;

    public static function generateSortValue(): string
    {
        return base_convert((string) hrtime(true), 10, 16);
    }
}
