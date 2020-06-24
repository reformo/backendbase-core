<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Command;

use BackendBase\Domain\User\Interfaces\UserRepository;
use BackendBase\Domain\User\Model\UserId;

class UpdateUserPartiallyHandler
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateUserPartially $command) : void
    {
        $payload = $command->payload();
        $this->repository->updateUserInfo(UserId::createFromString($command->id()), $payload);
    }
}
