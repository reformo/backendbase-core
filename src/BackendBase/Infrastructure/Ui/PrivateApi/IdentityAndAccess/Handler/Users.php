<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Domain\User\Interfaces\UserQuery;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use BackendBase\Shared\Services\MessageBus\Interfaces\QueryBus;

class Users implements RequestHandlerInterface
{
    private $config;
    private $queryBus;
    private UserQuery $userQuery;
    private RolesRepository $rolesRepository;

    public function __construct(
        QueryBus $queryBus,
        UserQuery $userQuery,
        RolesRepository $rolesRepository,
        array $config
    ) {
        $this->config          = $config;
        $this->queryBus        = $queryBus;
        $this->userQuery       = $userQuery;
        $this->rolesRepository = $rolesRepository;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Users::USERS_MENU) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to list users');
        }
        $limit     = 100;
        $usersData = $this->userQuery->getAllUsersPaginated(0, $limit);
        $users     = [];
        foreach ($usersData as $user) {
            $users[] = [
                'id' => $user->id(),
                'email' => $user->email(),
                'firstName' => $user->firstName(),
                'lastName' => $user->lastName(),
                'role' => $user->role(),
                'roleStr' => $user->roleStr(),
                'createdAt' => $user->createdAt(),
                'isActive' => $user->isActive(),
            ];
        }
        $rolesData = $this->rolesRepository->getUserRoleNames();
        $roles     = [];
        foreach ($rolesData as $roleItem) {
            $roles[$roleItem['key']] = $roleItem['title'];
        }

        return new JsonResponse(['users' => $users, 'roles' => $roles, 'pageSize' => $limit]);
    }
}
