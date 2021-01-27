<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\User\Interfaces\UserRepository;
use BackendBase\Domain\User\Model\UserId;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionDetails implements RequestHandlerInterface
{
    private UserRepository $userRepository;
    private RolesRepository $rolesRepository;

    public function __construct(
        UserRepository $userRepository,
        RolesRepository $rolesRepository
    ) {
        $this->userRepository  = $userRepository;
        $this->rolesRepository = $rolesRepository;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $this->userRepository
            ->getUserById(UserId::createFromString($request->getAttribute('loggedUserId')));

        $permissions = $this->rolesRepository->getRolePermissionsByRoleName($user->role());
        $data        = [
            'user' => [
                'id' => $user->id()->toString(),
                'firstName' => $user->firstName(),
                'lastName' => $user->lastName(),
                'email' => $user->email()->toString(),
                'role' => $user->role(),
                'permissions' => $permissions,
            ],
        ];

        return new JsonResponse($data, 200);
    }
}
