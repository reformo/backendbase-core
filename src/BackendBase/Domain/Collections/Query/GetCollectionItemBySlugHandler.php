<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Query;

use BackendBase\Domain\Collections\Interfaces\CollectionQuery;
use BackendBase\Domain\Collections\Persistence\Doctrine\ResultObject\Collection;

class GetCollectionItemBySlugHandler
{
    private CollectionQuery $collectionQuery;

    public function __construct(CollectionQuery $collectionQuery)
    {
        $this->collectionQuery = $collectionQuery;
    }

    public function __invoke(GetCollectionItemBySlug $query) : Collection
    {
        return $this->collectionQuery->findBySlug($query->parentId(), $query->slug());
    }
}
