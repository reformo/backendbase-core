<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Command;

use BackendBase\Domain\Collections\Interfaces\CollectionRepository;
use Ramsey\Uuid\Uuid;

class DeleteCollectionItemHandler
{
    private CollectionRepository $repository;

    public function __construct(CollectionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DeleteCollectionItem $command): void
    {
        $id      = Uuid::fromString($command->id());
        $payload = ['isDeleted' => 1];
        $this->repository->updateCollection($id, $payload);
    }
}
