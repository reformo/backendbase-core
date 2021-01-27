<?php

declare(strict_types=1);

namespace BackendBase\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Redislabs\Module\ReJSON\ReJSON;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class RolesRepository
{
    protected EntityManager $entityManager;
    protected Connection $connection;
    private ReJSON $reJSON;

    public function __construct(EntityManager $entityManager, Connection $connection, ReJSON $reJSON)
    {
        $this->connection    = $connection;
        $this->entityManager = $entityManager;
        $this->reJSON        = $reJSON;
    }

    public function getPermissionsTypesList(): array
    {
        $sql       = '
            SELECT name, slug
              FROM admin.permissions_types
             ORDER BY created_at ASC
        ';
        $statement = $this->connection->executeQuery($sql);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return [];
        }

        return $data;
    }

    public function getPermissionsList(): array
    {
        $sql       = '
            SELECT *
              FROM admin.permissions
             ORDER BY created_at ASC
        ';
        $statement = $this->connection->executeQuery($sql);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return [];
        }

        return $data;
    }

    public function getUserRoles(): array
    {
        $sql       = '
            SELECT *
              FROM admin.roles
              WHERE visible = 1
             ORDER BY created_at ASC
        ';
        $statement = $this->connection->executeQuery($sql);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return [];
        }

        return $data;
    }

    public function getUserRoleNames(): array
    {
        $sql       = '
            SELECT key, title
              FROM admin.roles
              WHERE visible = 1
             ORDER BY created_at ASC
        ';
        $statement = $this->connection->executeQuery($sql);
        $data      = $statement->fetchAll();
        if ($data === false) {
            return [];
        }

        return $data;
    }

    public function getRolePermissionsByRoleName(string $roleType): array
    {
        $sql       = '
            SELECT permissions
              FROM admin.roles
              WHERE key = :roleType
              LIMIT 1
        ';
        $statement = $this->connection->executeQuery($sql, ['roleType' => $roleType]);
        $data      = $statement->fetch();
        if ($data === false) {
            return [];
        }

        return json_decode($data['permissions'], true, 512, JSON_THROW_ON_ERROR);
    }

    public function getRolePermissionsByRoleNameForUser(string $userId): array
    {
        $sql       = '
            SELECT R.permissions                
              FROM admin.users U
              LEFT JOIN admin.roles R ON R.key = U.role
              WHERE U.id = :userId
                AND U.is_active = 1
                AND U.is_deleted = 0
              LIMIT 1
        ';
        $statement = $this->connection->executeQuery($sql, ['userId' => $userId]);
        $data      = $statement->fetch();
        if ($data === false) {
            return [];
        }

        return json_decode($data['permissions'], true, 512, JSON_THROW_ON_ERROR);
    }
}
