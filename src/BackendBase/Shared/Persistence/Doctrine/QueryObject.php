<?php

declare(strict_types=1);

namespace BackendBase\Shared\Persistence\Doctrine;

use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Shared\Services\CamelCaseReflectionHydrator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Statement;
use PDO;
use Selami\Stdlib\Arrays\ArrayKeysCamelCaseConverter;
use Throwable;

use function gettype;
use function strtr;

trait QueryObject
{
    private Connection $connection;

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

    protected function fetch(int $type, array $parameters, ?string $fetchObjectFullyQualifiedClassName = ''): ResultObject | iterable
    {
        $result      = $this->executeQuery(self::$sql, $parameters);
        $exceptionClass = self::NOT_FOUND_CLASS;
        try {
            $record = $result->fetchAssociative();
            if (empty($record)) {
                throw $exceptionClass::create($this->buildExceptionMessage($parameters));
            }

            if ($type === PDO::FETCH_OBJ) {
                return self::hydrate($record, $fetchObjectFullyQualifiedClassName);
            }

            return ArrayKeysCamelCaseConverter::convertArrayKeys($record);
        } catch (Throwable $exception) {
            if ($exception instanceof $exceptionClass) {
                throw $exception;
            }

            throw ExecutionFailed::create($exception->getMessage());
        }
    }

    protected function fetchAssociativeArray(array $parameters): iterable
    {
        return $this->fetch(PDO::FETCH_ASSOC, $parameters);
    }

    protected function fetchObject(array $parameters, string $fetchObjectFullyQualifiedClassName): ResultObject
    {
        return $this->fetch(PDO::FETCH_OBJ, $parameters, $fetchObjectFullyQualifiedClassName);
    }

    private function buildExceptionMessage($parameters): string
    {
        $params = [];
        foreach ($parameters as $key => $value) {
            $params[':' . $key] = $value;
        }

        return strtr(self::NOT_FOUND_MESSAGE, $params);
    }

    public function getExceptionMessage(?array $parameters = []): string
    {
        return $this->buildExceptionMessage($parameters);
    }

    protected function executeQuery(string $sql, array $parameters): Result
    {
        $statement = $this->connection
            ->prepare($sql);
        foreach ($parameters as $key => $value) {
            $statement->bindValue($key, $value, self::$types[gettype($value)] ?? PDO::PARAM_STR);
        }
        $result = $statement->executeQuery();
        return $result;
    }

    protected static function hydrate($data, string $fullyQualifiedClassName): ResultObject
    {
        return (new CamelCaseReflectionHydrator())
            ->hydrate($data, new $fullyQualifiedClassName());
    }
}
