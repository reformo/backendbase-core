<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Command;

use BackendBase\Domain\Administrators\Exception\UserNotFound;
use BackendBase\Domain\Administrators\Interfaces\UserRepository;
use BackendBase\Domain\Administrators\Model\UserId;
use BackendBase\Shared\Exception\ExecutionFailed;

class UnregisterUserHandler
{
    public function __construct(private UserRepository $repository)
    {
    }

    /**
     * @throws UserNotFound
     * @throws ExecutionFailed
     *
     * @var UnregisterUser
     */
    public function __invoke(UnregisterUser $command): void
    {
        $this->repository->unregisterUser(UserId::createFromString($command->id()));
    }
}
