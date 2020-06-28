<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory\Doctrine;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\VarDateTimeImmutableType;
use Doctrine\DBAL\Types\VarDateTimeType;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ConnectionFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : Connection
    {
        Type::overrideType('datetime', VarDateTimeType::class);
        Type::overrideType('datetimetz', VarDateTimeType::class);
        Type::overrideType('time', VarDateTimeType::class);

        Type::overrideType('datetime_immutable', VarDateTimeImmutableType::class);
        Type::overrideType('datetimetz_immutable', VarDateTimeImmutableType::class);
        Type::overrideType('time_immutable', VarDateTimeImmutableType::class);
        $config           = $container->get('config');
        $connectionParams = $config['doctrine']['dbal'];
        $doctrineConfig   = new Configuration();
        $conn             = DriverManager::getConnection($connectionParams, $doctrineConfig);
        $conn->getDatabasePlatform()->setUseBooleanTrueFalseStrings(false);

        return $conn;
    }
}
