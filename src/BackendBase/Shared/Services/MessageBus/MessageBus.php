<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services\MessageBus;

use League\Tactician\CommandBus as TacticianCommandBus;

class MessageBus
{
    private $messageBus;

    public function __construct(TacticianCommandBus $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * Executes the given command and optionally returns a value
     *
     * @return mixed
     *
     * @var object
     */
    public function handle(object $command)
    {
        return $this->messageBus->handle($command);
    }
}
