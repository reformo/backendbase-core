<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory\Doctrine;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Redislabs\Module\ReJSON\ReJSON;
use function str_replace;

final class DoctrineRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $connection          = $container->get(Connection::class);
        $entityManager       = $container->get(EntityManager::class);
        $rejson              = $container->get(ReJSON::class);
        $repositoryClassName = str_replace(['Interfaces', 'Repository'], ['Persistence\\Doctrine', ''], $requestedName);

        return new $repositoryClassName($entityManager, $connection, $rejson);
    }
}
