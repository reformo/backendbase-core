<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Persistence\Doctrine;

use BackendBase\Domain\Administrators\Exception\CantUnregisterUserDoesNotExists;
use BackendBase\Domain\Administrators\Interfaces\UserId;
use BackendBase\Domain\Administrators\Interfaces\UserRepository as UserRepositoryInterface;
use BackendBase\Domain\Administrators\Model\User;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetUserByEmail;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetUserById;
use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\ValueObject\Email;
use Carbon\CarbonImmutable;
use Throwable;

class UserRepository extends GenericRepository implements UserRepositoryInterface
{
    private const TABLE_NAME = 'users';

    public function getUserById(UserId $userId): ?User
    {
        $user = GetUserById::execute($this->connection, ['userId' => $userId->toString()]);

        return User::create(
            $user->id(),
            $user->email(),
            $user->firstName(),
            $user->lastName(),
            $user->passwordHash(),
            $user->role(),
            (bool) $user->isActive(),
            new CarbonImmutable($user->createdAt())
        );
    }

    public function getUserByEmail(Email $email): ?User
    {
        $user =  GetUserByEmail::execute($this->connection, ['email' => $email->toString()]);

        return User::create($user->id(), $user->email(), $user->firstName(), $user->lastName(), $user->passwordHash(), $user->role(), $user->createdAt());
    }

    public function registerUser(User $user): void
    {
        $doctrineRepository = UserMapper::toDoctrineEntity($user);
        $this->entityManager->persist($doctrineRepository);
        $this->entityManager->flush();
    }

    public function unregisterUser(UserId $userId): void
    {
        try {
            $this->getUserById($userId);
        } catch (Throwable $exception) {
            throw CantUnregisterUserDoesNotExists::create($exception->getMessage());
        }

        try {
            $this->connection->delete(
                self::TABLE_NAME,
                ['id' => $userId->id()->toString()]
            );
        } catch (Throwable $exception) {
            throw ExecutionFailed::create($exception->getMessage());
        }
    }

    public function updateUserInfo(UserId $userId, array $payload): void
    {
        $this->getUserById($userId);
        $this->connection->update(self::TABLE_NAME, $payload, ['id' => $userId->toString()]);
    }
}
