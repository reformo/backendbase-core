<?php

declare(strict_types=1);

namespace BackendBase\Shared\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use ReflectionClass;
use Selami\Stdlib\Resolver;

class RequestHandlerFactory implements FactoryInterface
{
    private ContainerInterface $container;

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RequestHandler
    {
        $this->container             = $container;
        $handlerConstructorArguments = Resolver::getParameterHints($requestedName, '__construct');
        $arguments                   = [];
        foreach ($handlerConstructorArguments as $argumentName => $argumentType) {
            $arguments[] = $this->getArgument($argumentName, $argumentType);
        }

        $handlerClass = new ReflectionClass($requestedName);

        /**
         * @var RequestHandler
         */
        return $handlerClass->newInstanceArgs($arguments);
    }

    private function getArgument(string $argumentName, string $argumentType)
    {
        return $this->container->has($argumentType) ? $this->container->get($argumentType) :
            $this->container->get($argumentName);
    }
}
