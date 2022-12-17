<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetPermissionsListByRole;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetUserById as GetUserByIdQuery;
use BackendBase\Domain\Administrators\Persistence\Doctrine\ResultObject\User;
use BackendBase\Shared\CQRS\Interfaces\QueryHandler;

class GetUserByIdHandler implements QueryHandler
{
    public function __construct(private GetUserByIdQuery $getUserByIdQuery, private GetPermissionsListByRole $getPermissionsListByRoleQuery)
    {
    }

    public function __invoke(GetUserById $query): User
    {
        $parameters = ['userId' => $query->userId()];
        $user       = $this->getUserByIdQuery->query($parameters);
        $user->unset('passwordHash', 'passwordHashAlgo');
        $user->setPermissions($this->getPermissionsListByRoleQuery->query(['roleType' => $user->role()]));

        return $user;
    }
}
