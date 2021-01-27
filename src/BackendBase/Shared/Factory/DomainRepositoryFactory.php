<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use Doctrine\DBAL\Driver\Connection;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

use function str_replace;

final class DomainRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $connection          = $container->get(Connection::class);
        $repositoryClassName = str_replace('Interfaces', 'Persistence\\Doctrine', $requestedName);

        return new $repositoryClassName($connection);
    }
}
