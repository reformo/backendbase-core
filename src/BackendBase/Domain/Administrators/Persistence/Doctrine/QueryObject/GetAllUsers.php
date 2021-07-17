<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject;

use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Domain\Shared\Exception\InvalidArgument;
use BackendBase\Domain\Administrators\Model\Users;
use BackendBase\Domain\Administrators\Persistence\Doctrine\ResultObject\User;
use BackendBase\Shared\Persistence\Doctrine\QueryObject;
use BackendBase\Shared\Persistence\QueryObject as QueryObjectInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\FetchMode;
use Throwable;
use Doctrine\Common\Collections\AbstractLazyCollection;

use function array_key_exists;

final class GetAllUsers extends AbstractLazyCollection implements QueryObjectInterface
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

    protected function doInitialize() : void
    {
        try {
            $records = $this->connection->fetchAllAssociative(self::$sql, $this->parameters);
        } catch (Throwable $exception) {
            throw ExecutionFailed::create($exception->getMessage());
        }
        $collection = [];
        foreach ($records as $record) {
            $collection[] = self::hydrate($record, User::class);
        }
        $this->collection = $collection;
    }


    public static function execute(Connection $connection, array $parameters): ?Collection
    {
        if (! array_key_exists('offset', $parameters)) {
            throw InvalidArgument::create('Query needs parameter named: offset');
        }

        if (! array_key_exists('limit', $parameters)) {
            throw InvalidArgument::create('Query needs parameter named: limit');
        }
        return new self($connection, $parameters);
       /* $query     = new static($connection);
        $statement = $query->executeQuery(self::$sql, $parameters);
        try {
            $records = $statement->fetchAll(FetchMode::CUSTOM_OBJECT, Administrators::class);
            return self::hydrate($records[0], Administrators::class);

            return new Users($records);
        } catch (Throwable $exception) {
            throw ExecutionFailed::create($exception->getMessage());
        }*/
    }
}
