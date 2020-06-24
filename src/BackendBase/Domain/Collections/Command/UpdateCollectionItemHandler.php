<?php

declare(strict_types=1);

namespace BackendBase\Domain\Collections\Command;

use BackendBase\Domain\Collections\Interfaces\CollectionRepository;
use Ramsey\Uuid\Uuid;

class UpdateCollectionItemHandler
{
    private CollectionRepository $repository;

    public function __construct(CollectionRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateCollectionItem $command) : void
    {
        $payload = $command->payload();
        $id      = Uuid::fromString($command->id());
        $this->repository->updateCollection($id, $payload);
    }
}
