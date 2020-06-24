<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Query;

use BackendBase\Domain\User\Interfaces\UserQuery;
use BackendBase\Domain\User\Model\Users;

class GetAllUsersHandler
{
    private $repository;

    public function __construct(UserQuery $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(GetAllUsers $command) : Users
    {
        return $this->repository->getAllUsersPaginated($command->offset(), $command->limit());
    }
}
