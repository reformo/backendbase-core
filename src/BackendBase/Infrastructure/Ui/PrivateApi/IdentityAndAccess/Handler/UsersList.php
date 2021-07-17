<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\Administrators\Query\GetAllUsersPaginated;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Shared\CQRS\Interfaces\QueryBus;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UsersList implements RequestHandlerInterface
{
    private QueryBus $queryBus;

    public function __construct(QueryBus $queryBus, array $config)
    {
        $this->queryBus = $queryBus;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Users::USERS_MENU) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to list users');
        }
        $limit  = 25;
        $offset = 0;
        $data = $this->queryBus->handle(new GetAllUsersPaginated($offset, $limit));

        return new JsonResponse($data);
    }
}
