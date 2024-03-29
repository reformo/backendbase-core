<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Collections\Handler;

use BackendBase\Domain\Collections\Command\AddNewCollectionItem as AddNewCollectionItemCommand;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Shared\Services\MessageBus\Interfaces\CommandBus;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Selami\Stdlib\Arrays\PayloadSanitizer;
use Ulid\Ulid;

use function count;
use function trim;

class AddNewCollectionItem implements RequestHandlerInterface
{
    private CommandBus $commandBus;

    private static array $requiredInputs = ['name', 'key', 'metadata', 'parentId', ''];

    public function __construct(
        CommandBus $commandBus,
        private array $config
    ) {
        $this->commandBus = $commandBus;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Collections::COLLECTIONS_CREATE) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to create a new collection');
        }

        $payload = $request->getParsedBody();

        $requestBody = PayloadSanitizer::sanitize($payload);

        $collectionItemName     = $requestBody['name'];
        $collectionItemKey      = trim($requestBody['key']);
        $collectionItemParentId = $requestBody['parentId'];
        $collectionItemMetadata = $requestBody['metadata'];
        if ((is_countable($collectionItemMetadata) ? count($collectionItemMetadata) : 0) === 0) {
            $collectionItemMetadata = ['isProtected' => true, 'isExposable' => false];
        }

        if (empty($collectionItemKey)) {
            $collectionItemKey = Ulid::generate();
        }

        $command = new AddNewCollectionItemCommand(
            $collectionItemName,
            $collectionItemKey,
            $collectionItemParentId,
            $collectionItemMetadata
        );
        $this->commandBus->handle($command);

        return new EmptyResponse(201, ['Location' => '/collections/' . $collectionItemKey]);
    }
}
