<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Persistence\Doctrine;

use BackendBase\Domain\Collections\Interfaces\CollectionRepository as CollectionRepositoryInterface;
use BackendBase\Domain\Collections\Model\Collection;
use BackendBase\Domain\Collections\Persistence\Doctrine\ResultObject\Collection as CollectionResultObject;
use BackendBase\Domain\Collections\Persistence\Doctrine\SqlQuery\GetCollectionItem;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\Collection as DoctrineCollectionEntity;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Redislabs\Module\ReJSON\ReJSON;

class CollectionRepository implements CollectionRepositoryInterface
{
    public function __construct(protected EntityManager $entityManager, protected Connection $connection, private ReJSON $reJSON)
    {
    }

    private function convertResultObjectToCollection(CollectionResultObject $persistedObject): Collection
    {
        return new Collection(
            Uuid::fromString($persistedObject->id()),
            $persistedObject->name(),
            $persistedObject->key(),
            $persistedObject->isActive(),
            $persistedObject->parentId() === null ? null : Uuid::fromString($persistedObject->parentId()),
            $persistedObject->metadata()
        );
    }

    private function convertCollectionToDoctrineEntity(Collection $collection): DoctrineCollectionEntity
    {
        $doctrineCollectionEntity = new DoctrineCollectionEntity();
        $doctrineCollectionEntity->setId($collection->id()->toString());
        $doctrineCollectionEntity->setKey($collection->key());
        $doctrineCollectionEntity->setName($collection->name());
        $doctrineCollectionEntity->setSlug($collection->slug());
        $doctrineCollectionEntity->setMetadata($collection->metadata());
        $doctrineCollectionEntity->setIsActive($collection->isActive());
        $doctrineCollectionEntity->setParentId($collection->parentId()->toString());

        return $doctrineCollectionEntity;
    }

    private function convertCollectionToDoctrineEntityWithPayload(Collection $collection, $payload): DoctrineCollectionEntity
    {
        $doctrineCollectionEntity = $this->entityManager->find(
            DoctrineCollectionEntity::class,
            $collection->id()->toString()
        );
        $doctrineCollectionEntity->setKey($payload['key'] ?? $collection->key());
        $doctrineCollectionEntity->setName($payload['name'] ?? $collection->name());
        $doctrineCollectionEntity->setSlug($payload['slug'] ?? $collection->slug());
        $doctrineCollectionEntity->setMetadata($payload['metadata'] ?? $collection->metadata());
        $doctrineCollectionEntity->setIsActive($payload['isActive'] ?? $collection->isActive());
        $doctrineCollectionEntity->setIsDeleted($payload['isDeleted'] ?? 0);
        $doctrineCollectionEntity->setParentId($payload['parentId'] ?? $collection->parentId()->toString());

        return $doctrineCollectionEntity;
    }

    public function findById(UuidInterface $id): Collection
    {
        $collectionItem  = new GetCollectionItem($this->connection);
        $persistedObject = $collectionItem->byId($id->toString());

        return $this->convertResultObjectToCollection($persistedObject);
    }

    public function findByKey(string $key): Collection
    {
        $collectionItem  = new GetCollectionItem($this->connection);
        $persistedObject = $collectionItem->byKey($key);

        return $this->convertResultObjectToCollection($persistedObject);
    }

    public function findBySlug(?UuidInterface $parentId, string $slug): Collection
    {
        $collectionItem  = new GetCollectionItem($this->connection);
        $persistedObject = $collectionItem->bySlug($parentId->toString(), $slug);

        return $this->convertResultObjectToCollection($persistedObject);
    }

    public function updateCollection(UuidInterface $id, array $payload): void
    {
        $collection               = $this->findById($id);
        $doctrineCollectionEntity = $this->convertCollectionToDoctrineEntityWithPayload($collection, $payload);
        $this->entityManager->persist($doctrineCollectionEntity);
        $this->entityManager->flush();
    }

    public function addNewCollection(Collection $collection): void
    {
        $doctrineCollectionEntity = $this->convertCollectionToDoctrineEntity($collection);
        $doctrineCollectionEntity->setIsDeleted(0);
        $this->entityManager->persist($doctrineCollectionEntity);
        $this->entityManager->flush();
    }
}
