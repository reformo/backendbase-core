<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Interfaces;

use BackendBase\Domain\Collections\Model\Collections;
use BackendBase\Domain\Collections\Persistence\Doctrine\ResultObject\Collection;

interface CollectionQuery
{
    public function findById(string $id): Collection;

    public function findByKey(string $key): Collection;

    public function findBySlug(string $parentId, string $slug): Collection;

    public function findSubItems(?string $parentId, int $offset, int $limit): Collections;
}
