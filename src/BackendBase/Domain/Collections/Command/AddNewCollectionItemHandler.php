<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Command;

use BackendBase\Domain\Collections\Exception\CollectionExists;
use BackendBase\Domain\Collections\Exception\CollectionNotFound;
use BackendBase\Domain\Collections\Interfaces\CollectionRepository;
use BackendBase\Domain\Collections\Model\Collection;
use Ramsey\Uuid\Uuid;
use function sprintf;

class AddNewCollectionItemHandler
{
    private CollectionRepository $repository;

    public function __construct(CollectionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(AddNewCollectionItem $command) : void
    {
        $payload            = $command->payload();
        $collectionRootItem = $this->repository->findByKey('lt:root');
        $collection         = new Collection(
            Uuid::uuid4(),
            $payload['name'],
            $payload['key'],
            Collection::IS_ACTIVE_DEFAULT,
            $payload['parentId'] === null ? $collectionRootItem->id() : Uuid::fromString($payload['parentId']),
            $payload['metadata'] ?? ['protected' => true],
        );
        try {
            $this->repository->findByKey($collection->key());
            throw CollectionExists::create(
                sprintf('Collection exists with the provided key %s ', $collection->key())
            );
        } catch (CollectionNotFound $e) {
        }
        try {
            $this->repository->findBySlug($collection->parentId(), $collection->slug());
            throw CollectionExists::create(
                sprintf('Collection exists with the provided parentId %s and slug %s ', $collection->parentId(), $collection->slug())
            );
        } catch (CollectionNotFound $e) {
        }
        $this->repository->addNewCollection($collection);
    }
}
