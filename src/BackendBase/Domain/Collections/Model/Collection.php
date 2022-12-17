<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Model;

use Cocur\Slugify\Slugify;
use Ramsey\Uuid\UuidInterface as Uuid;
use Webmozart\Assert\Assert;

class Collection
{
    public const CRITERIA_MIN_NAME_LENGTH = 1;
    public const CRITERIA_MIN_KEY_LENGTH  = 1;
    public const IS_ACTIVE_DEFAULT        = 1;
    private string $name;
    private string $key;

    public function __construct(private Uuid $id, string $name, string $key, private int $isActive, private ?Uuid $parentId, private ?array $metadata)
    {
        Assert::minLength($name, self::CRITERIA_MIN_NAME_LENGTH);
        Assert::minLength($key, self::CRITERIA_MIN_KEY_LENGTH);
        $this->name     = $name;
        $this->key      = $key;
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function slug(): string
    {
        $slugifier = new Slugify(['rulesets' => ['default', 'turkish']]);

        return $slugifier->slugify($this->name);
    }

    public function metadata(): ?array
    {
        return $this->metadata;
    }

    public function isActive(): int
    {
        return $this->isActive;
    }

    public function parentId(): Uuid
    {
        return $this->parentId;
    }
}
