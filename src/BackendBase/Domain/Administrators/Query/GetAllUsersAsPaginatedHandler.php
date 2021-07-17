<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Domain\Administrators\Interfaces\UserQuery;
use BackendBase\Domain\Administrators\Model\Users;
use BackendBase\Shared\CQRS\Interfaces\QueryHandler;

class GetAllUsersAsPaginatedHandler implements QueryHandler
{
    private UserQuery $repository;

    public function __construct(UserQuery $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetAllUsersAsPaginated $command): Users
    {
        return $this->repository->getAllUsersPaginated($command->offset(), $command->limit());
    }
}
