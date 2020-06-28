<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Persistence\Doctrine;

use BackendBase\Domain\User\Interfaces\UserId;
use BackendBase\Domain\User\Interfaces\UserQuery as UserQueryInterface;
use BackendBase\Domain\User\Model\Users;
use BackendBase\Domain\User\Persistence\Doctrine\ResultObject\User;
use BackendBase\Domain\User\Persistence\Doctrine\SqlQuery\GetAllUsers;
use BackendBase\Domain\User\Persistence\Doctrine\SqlQuery\GetUserByEmail;
use BackendBase\Domain\User\Persistence\Doctrine\SqlQuery\GetUserById;
use BackendBase\Shared\ValueObject\Interfaces\Email;
use Doctrine\DBAL\Driver\Connection;

class UserQuery implements UserQueryInterface
{
    private const TABLE_NAME = 'admin.users';
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getUserById(UserId $userId) : ?User
    {
        return GetUserById::execute($this->connection, ['userId' => $userId->toString()]);
    }

    public function getUserByEmail(Email $email) : ?User
    {
        return GetUserByEmail::execute($this->connection, ['email' => $email->toString()]);
    }

    public function getAllUsersPaginated(int $offset, int $limit) : Users
    {
        return GetAllUsers::execute($this->connection, ['offset' => $offset, 'limit' => $limit]);
    }
}
