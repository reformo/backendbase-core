<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Interfaces;

use BackendBase\Domain\Collections\Model\Collection;
use Ramsey\Uuid\UuidInterface as Uuid;

interface CollectionRepository
{
    public function addNewCollection(Collection $collection): void;

    public function updateCollection(Uuid $id, array $payload): void;

    public function findById(Uuid $id): Collection;

    public function findByKey(string $key): Collection;

    public function findBySlug(?Uuid $parentId, string $slug): Collection;
}
