<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;

use function get_class;

class ContainerAwareHandlersLocator implements HandlersLocatorInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getHandlers(Envelope $envelope): iterable
    {
        $message = $envelope->getMessage();
        yield new HandlerDescriptor($this->container->get(get_class($message)));
    }
}
