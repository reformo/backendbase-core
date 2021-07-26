<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Shared\CQRS\Interfaces\Command;
use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Throwable;

use function class_implements;
use function in_array;

class CommandHandlerFactory extends ReflectionBasedAbstractFactory
{
    public function canCreate(ContainerInterface $container, $requestedName): bool
    {
        return in_array(Command::class, class_implements($requestedName), true);
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): mixed
    {
        try {
            $reflection = new ReflectionClass($requestedName);
        } catch (Throwable $exception) {
            throw ExecutionFailed::create($exception->getMessage(), [
                'file' => $exception->getFile(),
                'trace' => $exception->getTrace(),
            ]);
        }

        $attributes = $reflection->getAttributes();
        $handler    = $attributes[0]->getArguments()['handler'] ?? $attributes[0]->getArguments()[0];

        return parent::__invoke($container, $handler, $options);
    }
}
