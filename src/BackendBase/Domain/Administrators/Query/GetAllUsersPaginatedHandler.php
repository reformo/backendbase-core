<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetAllUsers as GetAllUsersQuery;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetUserRoleNames as GetUserRoleNamesQuery;
use BackendBase\Domain\Shared\Exception\InvalidArgument;
use BackendBase\Shared\CQRS\Interfaces\QueryHandler;

use function Psl\Math\ceil;

class GetAllUsersPaginatedHandler implements QueryHandler
{
    public function __construct(
        private GetAllUsersQuery $getAllUsersQuery,
        private GetUserRoleNamesQuery $getUserRoleNamesQuery
    ) {
    }

    public function __invoke(GetAllUsersPaginated $message): array
    {
        $page        = $message->page();
        $queryString = $message->queryString();
        $total       = $this->getAllUsersQuery->getResourceTotal($queryString);
        $pageSize    = $message->pageSize();
        if ($pageSize > 1000) {
            $pageSize = 1000;
        }

        $pageLast = ceil($total / $pageSize);
        if ($page < 1) {
            throw InvalidArgument::create('Page number must be bigger than 0');
        }

        if ($page > $pageLast) {
            throw InvalidArgument::create('Page number can\'t be bigger thant total pages');
        }

        $parameters = ['offset' => ($page - 1) * $pageSize, 'limit' => $pageSize, 'queryString' => $queryString];
        $this->getAllUsersQuery->query($parameters);

        $users =  $this->getAllUsersQuery;
        $roles = [];

        foreach ($this->getUserRoleNamesQuery as $roleItem) {
            $roles[$roleItem['key']] = $roleItem['title'];
        }

        return [
            'users' => $users->toArray(),
            'roles' => $roles,
            '_page' => $page,
            '_total' => $total,
            '_perPage' => $pageSize,
        ];
    }
}
