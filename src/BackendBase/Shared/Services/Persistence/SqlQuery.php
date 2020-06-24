<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services\Persistence;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Statement;
use PDO;
use function gettype;

trait SqlQuery
{
    private Connection $connection;
    private array $parameters;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    private static array $types = [
        'integer' => PDO::PARAM_INT,
        'double' =>  PDO::PARAM_STR,
        'boolean' => PDO::PARAM_BOOL,
        'string' => PDO::PARAM_STR,
        'null' => PDO::PARAM_NULL,
    ];

    protected function executeQuery(string $sql, array $parameters) : Statement
    {
        $statement = $this->connection
            ->prepare($sql);
        foreach ($parameters as $key => $value) {
            $statement->bindValue($key, $value, self::$types[gettype($value)] ?? PDO::PARAM_STR);
        }
        $statement->execute();

        return $statement;
    }
}
