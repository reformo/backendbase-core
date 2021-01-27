<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Persistence\Doctrine\SqlQuery;

use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Domain\Shared\Exception\InvalidArgument;
use BackendBase\Domain\User\Model\Users;
use BackendBase\Domain\User\Persistence\Doctrine\ResultObject\User;
use BackendBase\Shared\Services\Persistence\SqlQuery;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\FetchMode;
use Throwable;

use function array_key_exists;

final class GetAllUsers
{
    use SqlQuery;

    private static $sql = <<<SQL
        SELECT U.id, 
               U.email, 
               U.first_name, 
               U.last_name, 
               U.password_hash,
               U.password_hash_algo, 
               U.role, 
               U.created_at, 
               U.is_active,
               R.title as role_str
          FROM admin.users U
          LEFT JOIN admin.roles R On R.key = U.role
         WHERE U.is_deleted = 0 
         ORDER BY R.created_at ASC, U.first_name ASC
         OFFSET :offset
         LIMIT  :limit
SQL;

    public static function execute(Connection $connection, array $parameters): ?Users
    {
        if (! array_key_exists('offset', $parameters)) {
            throw InvalidArgument::create('Query needs parameter named: offset');
        }

        if (! array_key_exists('limit', $parameters)) {
            throw InvalidArgument::create('Query needs parameter named: limit');
        }

        $query     = new static($connection);
        $statement = $query->executeQuery(self::$sql, $parameters);
        try {
            $records = $statement->fetchAll(FetchMode::CUSTOM_OBJECT, User::class);

            return new Users($records);
        } catch (Throwable $exception) {
            throw ExecutionFailed::create($exception->getMessage());
        }
    }
}
