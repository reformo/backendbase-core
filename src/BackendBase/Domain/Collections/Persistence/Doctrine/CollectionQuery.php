<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Persistence\Doctrine;

use BackendBase\Domain\Collections\Interfaces\CollectionQuery as CollectionQueryInterface;
use BackendBase\Domain\Collections\Model\Collections;
use BackendBase\Domain\Collections\Persistence\Doctrine\ResultObject\Collection as CollectionResultObject;
use BackendBase\Domain\Collections\Persistence\Doctrine\SqlQuery\GetCollectionItem;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Redislabs\Module\ReJSON\ReJSON;

use function array_keys;
use function array_multisort;
use function array_values;
use function json_decode;

use const JSON_OBJECT_AS_ARRAY;
use const SORT_NATURAL;

class CollectionQuery implements CollectionQueryInterface
{
    public function __construct(protected EntityManager $entityManager, protected Connection $connection, private ReJSON $reJSON)
    {
    }

    public function findById(string $id): CollectionResultObject
    {
        $collectionItem = new GetCollectionItem($this->connection);

        return $collectionItem->byId($id);
    }

    public function findByKey(string $key): CollectionResultObject
    {
        $collectionItem = new GetCollectionItem($this->connection);

        return $collectionItem->byKey($key);
    }

    public function findBySlug(?string $parentId, string $slug): CollectionResultObject
    {
        $collectionItem = new GetCollectionItem($this->connection);

        return $collectionItem->bySlug($parentId, $slug);
    }

    public function findSubItems(?string $parentId, int $offset, int $limit): Collections
    {
         $collectionItem = new GetCollectionItem($this->connection);

        return $collectionItem->subItemsById($parentId, $offset, $limit);
    }

    public function buildLookupData(): array
    {
        $sql = "
            SELECT LT.*, LTP.key as parent_key
              FROM lookup_table LT
              LEFT JOIN lookup_table LTP ON LTP.id=LT.parent_id
             WHERE LT.is_active = 1
               AND LT.is_deleted = 0
               AND LT.key != 'lt:root' 
       ";

        $statement   = $this->connection->executeQuery($sql);
        $data        =  $statement->fetchAll();
        $lookupTable = [];
        foreach ($data as $item) {
            $lookupTable[] = [
                'parent' => $item['parent_key'],
                'id' => $item['key'],
                'name' => $item['name'],
                'slug' => $item['slug'],
                'metadata' => json_decode($item['metadata'] ?? '{}', (bool) JSON_OBJECT_AS_ARRAY, 512, JSON_THROW_ON_ERROR),
                'items' => $this->getChildrenKeys($item['id']),
            ];
        }

        return $lookupTable;
    }

    public function buildLookupTable(): array
    {
        $sql = "
            SELECT LT.key, LT.name
              FROM lookup_table LT
             WHERE LT.is_active = 1
               AND LT.is_deleted = 0
               AND LT.key != 'lt:root' 
             ORDER BY LT.name
       ";

        $statement   = $this->connection->executeQuery($sql);
        $data        =  $statement->fetchAll();
        $lookupTable = [];
        foreach ($data as $item) {
            $lookupTable[$item['key']] = $item['name'];
        }

        return $lookupTable;
    }

    private function getChildrenKeys(string $parentId): array
    {
        $sql = '
            SELECT LT.key, LT.name
              FROM lookup_table LT
             WHERE LT.is_active = 1
               AND LT.parent_id = :parentId            
       ';

        $statement = $this->connection->executeQuery($sql, ['parentId' => $parentId]);
        $data      =  $statement->fetchAll();
        $keysData  = [];
        foreach ($data as $item) {
            $keysData[$item['name']] = $item['key'];
        }

        $arrayKeys = array_keys($keysData);
        array_multisort($arrayKeys, SORT_NATURAL, $keysData);

        return array_values($keysData);
    }
}
