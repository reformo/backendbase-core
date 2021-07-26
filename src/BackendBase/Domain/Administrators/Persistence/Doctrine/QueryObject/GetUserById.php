<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject;

use BackendBase\Domain\Administrators\Exception\UserNotFound;
use BackendBase\Domain\Administrators\Persistence\Doctrine\ResultObject\User;
use BackendBase\Domain\Shared\Exception\InvalidArgument;
use BackendBase\Shared\Persistence\Doctrine\QueryObject;
use BackendBase\Shared\Persistence\QueryObject as QueryObjectInterface;
use Doctrine\DBAL\Driver\Connection;

use function array_key_exists;

final class GetUserById implements QueryObjectInterface
{
    use QueryObject;

    private const NOT_FOUND_CLASS   = UserNotFound::class;
    private const NOT_FOUND_MESSAGE = 'Administrators not found by email: :email';

    private static string $sql = <<<SQL
        SELECT id,
               first_name,
               last_name,
               email,
               password_hash,
               password_hash_algo,
               is_active,
               created_at,
               role
          FROM admin.users
         WHERE id=:userId
           AND is_deleted = 0 
SQL;

    public static function execute(Connection $connection, array $parameters): ?User
    {
        if (! array_key_exists('userId', $parameters)) {
            throw InvalidArgument::create('Query needs parameter named: userId');
        }

        $query = new self($connection);

        return $query->query($parameters);
    }

    public function query(array |null $parameters = []): User| null
    {
        return $this->fetchObject($parameters, User::class);
    }
}
