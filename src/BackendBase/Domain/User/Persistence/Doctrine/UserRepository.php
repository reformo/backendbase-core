<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Persistence\Doctrine;

use BackendBase\Domain\User\Exception\CantUnregisterUserDoesNotExists;
use BackendBase\Domain\User\Exception\UserAlreadyExists;
use BackendBase\Domain\User\Exception\UserNotFound;
use BackendBase\Domain\User\Interfaces\UserId;
use BackendBase\Domain\User\Interfaces\UserRepository as UserRepositoryInterface;
use BackendBase\Domain\User\Model\User;
use BackendBase\Domain\User\Persistence\Doctrine\SqlQuery\GetUserByEmail;
use BackendBase\Domain\User\Persistence\Doctrine\SqlQuery\GetUserById;
use BackendBase\Shared\Exception\ExecutionFailed;
use BackendBase\Shared\Exception\InvalidArgument;
use BackendBase\Shared\ValueObject\Email;
use Doctrine\DBAL\Driver\Connection;
use Throwable;
use function sprintf;

class UserRepository implements UserRepositoryInterface
{
    private const TABLE_NAME = 'users';
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getUserById(UserId $userId) : ?User
    {
        $user = GetUserById::execute($this->connection, ['userId' => $userId->toString()]);

        return User::create($user->id(), $user->email(), $user->firstName(), $user->lastName(), $user->passwordHash(), $user->role(), $user->createdAt());
    }

    public function getUserByEmail(Email $email) : ?User
    {
        $user =  GetUserByEmail::execute($this->connection, ['email' => $email->toString()]);

        return User::create($user->id(), $user->email(), $user->firstName(), $user->lastName(), $user->passwordHash(), $user->role(), $user->createdAt());
    }

    public function registerUser(User $user) : void
    {
        try {
            $this->getUserByEmail($user->email());
            throw UserAlreadyExists::create(
                sprintf('User already exists with the email provided: %s', $user->email()->toString()),
                ['provided_email' => $user->email()->toString()]
            );
        } catch (UserNotFound $exception) {
            if (! $user instanceof User) {
                throw InvalidArgument::create('Provided data is not a User object!');
            }
            $mapper = new UserMapper($user);
            $this->connection->insert('users', $mapper->toDatabasePayload());
        }
    }

    /**
     * @inheritDoc
     */
    public function unregisterUser(UserId $userId) : void
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

    public function updateUserInfo(UserId $userId, array $payload) : void
    {
        $this->getUserById($userId);
        $this->connection->update(self::TABLE_NAME, $payload, ['id' => $userId->toString()]);
    }
}
