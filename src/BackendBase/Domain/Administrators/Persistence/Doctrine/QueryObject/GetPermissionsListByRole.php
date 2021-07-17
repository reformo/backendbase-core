<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject;

use BackendBase\Domain\Shared\Exception\InvalidArgument;
use BackendBase\Domain\Administrators\Exception\UserNotFound;
use BackendBase\Domain\Administrators\Persistence\Doctrine\ResultObject\User;
use BackendBase\Shared\Persistence\Doctrine\QueryObject;
use BackendBase\Shared\Persistence\QueryObject as QueryObjectInterface;
use Doctrine\DBAL\Driver\Connection;

use function array_key_exists;

final class GetPermissionsListByRole implements QueryObjectInterface
{
    use QueryObject;

    private const NOT_FOUND_CLASS = UserNotFound::class;
    private const NOT_FOUND_MESSAGE = 'Administrators not found by email: :email';

    private static string $sql = <<<SQL
             SELECT permissions
              FROM admin.roles
              WHERE key = :roleType
              LIMIT 1
SQL;

    public static function execute(Connection $connection, array $parameters): ? array
    {
        if (! array_key_exists('userId', $parameters)) {
            throw InvalidArgument::create('Query needs parameter named: userId');
        }
        $query = new self($connection);

        return $query->query($parameters);
    }

    public function query($parameters) : array
    {
        $permissions = $this->fetchAssociativeArray($parameters);
        return json_decode($permissions['permissions'], true, 512, JSON_THROW_ON_ERROR);
    }
}
