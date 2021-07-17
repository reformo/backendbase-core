<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Persistence\Doctrine;

use BackendBase\Domain\Administrators\Interfaces\UserId;
use BackendBase\Domain\Administrators\Interfaces\UserQuery as UserQueryInterface;
use BackendBase\Domain\Administrators\Model\Users;
use BackendBase\Domain\Administrators\Persistence\Doctrine\ResultObject\User;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetAllUsers;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetUserByEmail;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetUserById;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Shared\ValueObject\Interfaces\Email;

class UserQuery extends GenericRepository implements UserQueryInterface
{
    private const TABLE_NAME = 'admin.users';

    public function getUserById(UserId $userId): ?User
    {
        return GetUserById::execute($this->connection, ['userId' => $userId->toString()]);
    }

    public function getUserByEmail(Email $email): ?User
    {
        return GetUserByEmail::execute($this->connection, ['email' => $email->toString()]);
    }

    public function getAllUsersPaginated(int $offset, int $limit): Users
    {
        return GetAllUsers::execute($this->connection, ['offset' => $offset, 'limit' => $limit]);
    }
}
