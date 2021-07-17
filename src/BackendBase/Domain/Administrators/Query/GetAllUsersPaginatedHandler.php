<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetAllUsers as GetAllUsersQuery;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetUserRoleNames as GetUserRoleNamesQuery;
use BackendBase\Shared\CQRS\Interfaces\QueryHandler;
use Doctrine\Common\Collections\Collection;

class GetAllUsersPaginatedHandler implements QueryHandler
{
    private GetAllUsersQuery $getAllUsersQuery;
    private GetUserRoleNamesQuery $getUserRoleNamesQuery;

    public function __construct(GetAllUsersQuery $getAllUsersQuery, GetUserRoleNamesQuery $getUserRoleNamesQuery)
    {
        $this->getAllUsersQuery      = $getAllUsersQuery;
        $this->getUserRoleNamesQuery = $getUserRoleNamesQuery;
    }

    public function __invoke(GetAllUsersPaginated $message): array
    {
        $parameters = ['offset' => $message->offset(), 'limit' => $message->limit()];
        $this->getAllUsersQuery->query($parameters);

        /**
         * @var $users Collection
         */
        $users =  $this->getAllUsersQuery;
        $roles = [];

        /**
         * @var $rolesData Collection
         */
        $rolesData = $this->getUserRoleNamesQuery;
        foreach ($rolesData as $roleItem) {
            $roles[$roleItem['key']] = $roleItem['title'];
        }

        return [
            'users' => $users->toArray(),
            'roles' => $roles,
            'pageSize' => $message->limit(),
        ];
    }
}
