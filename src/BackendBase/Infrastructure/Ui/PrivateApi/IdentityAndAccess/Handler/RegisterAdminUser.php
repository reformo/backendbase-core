<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\Administrators\Command\RegisterUser;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Shared\CQRS\Interfaces\CommandBus;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use Selami\Stdlib\Arrays\PayloadSanitizer;

class RegisterAdminUser implements RequestHandlerInterface
{
    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Users::USERS_CREATE) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to create a user');
        }

        $payload = PayloadSanitizer::sanitize($request->getParsedBody());

        $message = new RegisterUser(Uuid::uuid4()->toString(), [
            'firstName' => $payload['firstName'],
            'lastName' => $payload['lastName'],
            'email' => $payload['email'],
            'passwordHash' => $payload['passwordHash'],
            'role' => $payload['role'] ?? 'editor',
        ]);

        $this->commandBus->dispatch($message);

        return new EmptyResponse(204, ['storage-Insert-Id' => $message->id()]);
    }
}
