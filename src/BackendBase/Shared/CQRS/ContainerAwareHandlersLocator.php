<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocatorInterface;

use function get_class;

class ContainerAwareHandlersLocator implements HandlersLocatorInterface
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function getHandlers(Envelope $envelope): iterable
    {
        $message = $envelope->getMessage();

        yield new HandlerDescriptor($this->container->get($message::class));
    }
}
