<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Persistence\Doctrine\SqlQuery;

use BackendBase\Domain\Collections\Exception\CollectionNotFound;
use BackendBase\Domain\Collections\Model\Collections;
use BackendBase\Domain\Collections\Persistence\Doctrine\ResultObject\Collection;
use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Shared\Persistence\Doctrine\QueryObject;
use Doctrine\DBAL\FetchMode;
use Throwable;

use function count;
use function sprintf;

final class GetCollectionItem
{
    use QueryObject;

    private static string $byIdSql      = <<<SQL
        SELECT id, key, name, slug, parent_id, metadata, is_active
          FROM public.lookup_table
         WHERE id=:collectionId
           AND is_deleted = 0
         LIMIT 1
SQL;
    private static string $byKeySql     = <<<SQL
        SELECT L.id, L.key, L.name, L.slug, L.parent_id, L.metadata, L.is_active, L2.key as parent_key
          FROM public.lookup_table L
          LEFT JOIN public.lookup_table L2 ON L2.id = L.parent_id
         WHERE L.key=:collectionKey
           AND L.is_deleted = 0
         LIMIT 1
SQL;
    private static string $bySlugSql    = <<<SQL
        SELECT id, key, name, slug, parent_id, metadata, is_active
          FROM public.lookup_table
         WHERE slug=:slug 
           AND parent_id = :parentId
           AND is_deleted = 0
         LIMIT 1
SQL;
    private static string $subItemsSql  = <<<SQL
        SELECT id, key, name, slug, parent_id, metadata, is_active
          FROM public.lookup_table
         WHERE parent_id = :parentId
           AND is_deleted = 0
        ORDER BY name asc
         LIMIT :limit
         OFFSET :offset
SQL;
    private static string $rootItemsSql = <<<SQL
        SELECT id, key, name, slug, parent_id, metadata, is_active
          FROM public.lookup_table
         WHERE parent_id IS NULL
           AND is_deleted = 0
         LIMIT :limit
         OFFSET :offset
SQL;

    public function byId(string $id): ?Collection
    {
        return $this->execute(self::$byIdSql, ['collectionId' => $id], 'id', 'collectionId');
    }

    public function byKey(string $key): ?Collection
    {
        return $this->execute(self::$byKeySql, ['collectionKey' => $key], 'key', 'collectionKey');
    }

    public function bySlug(?string $parentId, string $slug): ?Collection
    {
        return $this->execute(self::$bySlugSql, ['slug' => $slug, 'parentId' => $parentId], 'slug', 'slug');
    }

    public function subItemsById(?string $id, ?int $offset = 0, ?int $limit = 25): Collections
    {
        $sql       = $id === null ? self::$rootItemsSql : self::$subItemsSql;
        $params    = $id === null ? ['offset' => $offset, 'limit' => $limit] : ['parentId' => $id, 'offset' => $offset, 'limit' => $limit];
        $statement = $this->executeQuery($sql, $params);
        try {
            $records = $statement->fetchAll(FetchMode::CUSTOM_OBJECT, Collection::class);
            if (count($records) === 0) {
                return new Collections();
            }

            return new Collections($records);
        } catch (Throwable $exception) {
            throw ExecutionFailed::create($exception->getMessage());
        }
    }

    public function execute(string $sql, array $parameters, string $paramName, string $keyParameter): ?Collection
    {
        $statement = $this->executeQuery($sql, $parameters);
        try {
            $records = $statement->fetchAll(FetchMode::CUSTOM_OBJECT, Collection::class);
            if (count($records) === 0) {
                throw CollectionNotFound::create(
                    sprintf('Collection not found by ' . $paramName . ': %s', $parameters[$keyParameter])
                );
            }

            return $records[0];
        } catch (Throwable $exception) {
            if ($exception instanceof  CollectionNotFound) {
                throw $exception;
            }

            throw ExecutionFailed::create($exception->getMessage());
        }
    }
}
