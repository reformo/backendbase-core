<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Query;

use BackendBase\Domain\Collections\Interfaces\CollectionQuery;
use BackendBase\Domain\Collections\Model\Collections;

class GetCollectionItemsHandler
{
    private CollectionQuery $collectionQuery;

    public function __construct(CollectionQuery $collectionQuery)
    {
        $this->collectionQuery = $collectionQuery;
    }

    public function __invoke(GetCollectionItems $query) : Collections
    {
        $parentId = $query->parentId();
        if ($parentId === null) {
            $collectionRootItem = $this->collectionQuery->findByKey('lt:root');
            $parentId           = $collectionRootItem->id();
        }

        return $this->collectionQuery->findSubItems($parentId, $query->offset(), $query->limit());
    }
}
