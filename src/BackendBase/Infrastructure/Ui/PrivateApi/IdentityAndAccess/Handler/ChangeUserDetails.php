<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Domain\User\Interfaces\UserRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\User;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\Services\PayloadSanitizer;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ChangeUserDetails implements RequestHandlerInterface
{
    private UserRepository $userRepository;
    private GenericRepository $genericRepository;

    public function __construct(
        UserRepository $userRepository,
        GenericRepository $genericRepository
    ) {
        $this->userRepository    = $userRepository;
        $this->genericRepository = $genericRepository;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $userId = $request->getAttribute('userId');
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Users::USERS_EDIT) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to change a user details');
        }
        $payload = PayloadSanitizer::sanitize($request->getParsedBody());
        $this->genericRepository->updateGeneric(User::class, $userId, $payload);

        return new EmptyResponse(204);
    }
}
