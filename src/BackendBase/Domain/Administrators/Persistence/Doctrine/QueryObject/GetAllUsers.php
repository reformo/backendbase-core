<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject;

use BackendBase\Domain\Administrators\Persistence\Doctrine\ResultObject\User;
use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Domain\Shared\Exception\InvalidArgument;
use BackendBase\Shared\Persistence\Doctrine\QueryObject;
use BackendBase\Shared\Persistence\QueryObject as QueryObjectInterface;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Throwable;

use function array_key_exists;

final class GetAllUsers extends AbstractLazyCollection implements QueryObjectInterface
{
    use QueryObject;

    private array $parameters;

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

    protected function doInitialize(): void
    {
        try {
            $records = $this->connection->fetchAllAssociative(self::$sql, $this->parameters);
        } catch (Throwable $exception) {
            throw ExecutionFailed::create($exception->getMessage());
        }

        $collection = [];
        foreach ($records as $record) {
            $user = self::hydrate($record, User::class);
            $user->unset('passwordHash', 'passwordHashAlgo');
            $collection[] = $user;
        }

        $this->collection = new ArrayCollection($collection);
    }

    public function query(?array $parameters = []): void
    {
        if (! array_key_exists('offset', $parameters)) {
            throw InvalidArgument::create('Query needs parameter named: offset');
        }

        if (! array_key_exists('limit', $parameters)) {
            throw InvalidArgument::create('Query needs parameter named: limit');
        }

        $this->parameters = $parameters;
    }
}
