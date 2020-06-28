<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Collections\Handler;

use BackendBase\Domain\Collections\Command\UpdateCollectionItem as UpdateCollectionItemCommand;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Shared\Services\MessageBus\Interfaces\CommandBus;
use BackendBase\Shared\Services\PayloadSanitizer;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UpdateCollectionItem implements RequestHandlerInterface
{
    private $config;
    private $commandBus;

    public function __construct(
        CommandBus $commandBus,
        array $config
    ) {
        $this->config     = $config;
        $this->commandBus = $commandBus;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Collections::COLLECTIONS_EDIT) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to edit collections');
        }
        $id      = $request->getAttribute('collectionId');
        $payload = PayloadSanitizer::sanitize($request->getParsedBody());
        $query   = new UpdateCollectionItemCommand($id, $payload);
        $this->commandBus->handle($query);

        return new EmptyResponse(204);
    }
}
