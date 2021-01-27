<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Domain\User\Exception\UserAlreadyExists;
use BackendBase\Domain\User\Interfaces\UserRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\User;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\Services\PayloadSanitizer;
use BackendBase\Shared\ValueObject\Email;
use DateTimeImmutable;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;
use Throwable;

use function ucfirst;

class AddUser implements RequestHandlerInterface
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

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Users::USERS_CREATE) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to create a user');
        }

        $payload   = PayloadSanitizer::sanitize($request->getParsedBody());
        $userExist = false;
        try {
            $userExist = $this->userRepository->getUserByEmail(Email::createFromString($payload['email']));
        } catch (Throwable $e) {
        }

        if ($userExist !== false) {
            throw UserAlreadyExists::create('User with this email exists');
        }

        $user = new User();
        $user->setId(Uuid::uuid4()->toString());
        foreach ($payload as $key => $value) {
            $method = 'set' . ucfirst($key);
            $user->{$method}($value);
        }

        $user->setCreatedAt(new DateTimeImmutable());

        $this->genericRepository->persistGeneric($user);

        return new EmptyResponse(204, ['storage-Insert-Id' => $user->id()]);
    }
}
