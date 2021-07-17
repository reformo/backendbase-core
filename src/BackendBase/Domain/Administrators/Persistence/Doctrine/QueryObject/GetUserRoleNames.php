<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject;

use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Shared\Persistence\Doctrine\QueryObject;
use BackendBase\Shared\Persistence\QueryObject as QueryObjectInterface;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Throwable;

final class GetUserRoleNames extends AbstractLazyCollection implements QueryObjectInterface
{
    use QueryObject;

    private static string $sql = <<<SQL
        SELECT key, title
              FROM admin.roles
              WHERE visible = 1
             ORDER BY created_at ASC
SQL;

    protected function doInitialize(): void
    {
        try {
            $records = $this->connection->fetchAllAssociative(self::$sql, []);
        } catch (Throwable $exception) {
            throw ExecutionFailed::create($exception->getMessage());
        }

        $this->collection = new ArrayCollection($records);
    }

    public function query(?array $parameters = []): void
    {
    }
}
