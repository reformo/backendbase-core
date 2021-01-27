<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory\Doctrine;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

use function str_replace;

final class DoctrineRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $connection    = $container->get(Connection::class);
        $entityManager = $container->get(EntityManager::class);

        $repositoryClassName = str_replace(['Domain', 'Repository'], ['Infrastructure\\Persistence\\Doctrine', ''], $requestedName);

        return new $repositoryClassName($entityManager, $connection);
    }
}
