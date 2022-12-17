<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Query;

use BackendBase\Domain\Collections\Interfaces\CollectionQuery;
use BackendBase\Domain\Collections\Persistence\Doctrine\ResultObject\Collection;

class GetCollectionItemByIdHandler
{
    public function __construct(private CollectionQuery $collectionQuery)
    {
    }

    public function __invoke(GetCollectionItemById $query): Collection
    {
        return $this->collectionQuery->findById($query->id());
    }
}
