<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Repository;

use BackendBase\Shared\Persistence\Doctrine\Repository;
use BackendBase\Shared\Services\ArrayKeysCamelCaseConverter;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Redislabs\Module\RedisJson\RedisJsonInterface;

use function array_key_exists;
use function count;
use function json_decode;
use function ucfirst;

use const JSON_THROW_ON_ERROR;

class GenericRepository implements Repository
{
    protected EntityManagerInterface $entityManager;
    protected Connection $connection;
    protected RedisJsonInterface $redisJson;
    protected array $config;

    public function __construct(EntityManagerInterface $entityManager, RedisJsonInterface $redisJson, array $config)
    {
        $this->connection    = $entityManager->getConnection();
        $this->entityManager = $entityManager;
        $this->redisJson     = $redisJson;
        $this->config        = $config;
    }

    public function find(string $className, string $entityId)
    {
        return $this->entityManager->find($className, $entityId);
    }

    public function findGAsArray(string $className, string $entityId)
    {
        return $this->entityManager->getRepository($className)->findBy(['id' => $entityId])[0] ?? null;
    }

    public function persistGeneric($entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function addQueueToPersist($entity): void
    {
        $this->entityManager->persist($entity);
    }

    public function addQueueToRemove($entity): void
    {
        $this->entityManager->remove($entity);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    public function update(string $className, string $entityId, array $entityData): void
    {
        $genericEntityMeta     = $this->entityManager->getClassMetadata($className);
        $doctrineGenericEntity = $this->entityManager->find($className, $entityId);
        foreach ($entityData as $key => $value) {
            if (! $genericEntityMeta->hasField($key)) {
                continue;
            }

            $method = 'set' . ucfirst($key);
            $doctrineGenericEntity->{$method}($value);
        }

        $this->entityManager->persist($doctrineGenericEntity);
        $this->entityManager->flush();
    }

    public function getList(string $className, array $criteria, ?string $orderByString = '', ?array $pagination = []): array
    {
        $genericEntityMeta = $this->entityManager->getClassMetadata($className);
        $tableName         = $genericEntityMeta->getTableName();
        $columns           = $genericEntityMeta->getFieldNames();
        $whereSQL          = '';
        $select            = '*';
        $offset            = '';
        $limit             = '';
        $orderBy           = '';

        if (count($criteria) > 0) {
            $whereSQL = ' WHERE ';
            $useAnd   = 0;
            foreach ($criteria as $key => $value) {
                if ($useAnd === 1) {
                    $whereSQL .= ' AND ';
                }

                $whereSQL .= $key . '= :' . $key;
                $useAnd    = 1;
            }
        }

        if (array_key_exists('offset', $pagination)) {
            $offset             = 'OFFSET :offset';
            $criteria['offset'] = $pagination['offset'];
        }

        if (array_key_exists('limit', $pagination)) {
            $limit             = 'LIMIT :limit';
            $criteria['limit'] = $pagination['limit'];
        }

        if (! empty($orderByString)) {
            $orderBy = ' ORDER BY ' . $orderByString;
        }

        $sql       = <<<SQL
            SELECT {$select}
              FROM {$tableName}
              {$whereSQL}
            {$orderBy}
            {$offset}
            {$limit}
SQL;
        $statement = $this->connection->executeQuery($sql, $criteria);
        $data      = $statement->fetchAllAssociative();

        $returnData = [];
        foreach ($data as $datum) {
            foreach ($columns as $column) {
                $mappingData = $genericEntityMeta->getFieldMapping($column);
                if ($mappingData['type'] !== 'json' && $mappingData['type'] !== 'jsonb') {
                    continue;
                }

                $datum[$mappingData['columnName']] = json_decode($datum[$mappingData['columnName']], true, 512, JSON_THROW_ON_ERROR);
            }

            if (array_key_exists('passwordHash', $datum)) {
                unset($datum['passwordHash']);
            }

            $returnData[] = $datum;
        }

        return ArrayKeysCamelCaseConverter::convertArrayKeys($returnData);
    }

    public function getListTotal(string $className, array $criteria): int
    {
        $genericEntityMeta = $this->entityManager->getClassMetadata($className);
        $tableName         = $genericEntityMeta->getTableName();
        $whereSQL          = '';

        if (count($criteria) > 0) {
            $whereSQL = ' WHERE ';
            $useAnd   = 0;
            foreach ($criteria as $key => $value) {
                if ($useAnd === 1) {
                    $whereSQL .= ' AND ';
                }

                $whereSQL .= $key . '= :' . $key;
                $useAnd    = 1;
            }
        }

        $sql = <<<SQL
            SELECT count(*) as count
              FROM {$tableName}
              {$whereSQL}
SQL;

        $statement = $this->connection->executeQuery($sql, $criteria);

        return (int) $statement->fetchAssociative()['count'];
    }
}
