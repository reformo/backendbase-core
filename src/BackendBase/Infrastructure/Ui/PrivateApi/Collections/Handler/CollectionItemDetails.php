<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Collections\Handler;

use BackendBase\Domain\Collections\Query\GetCollectionItemByKey;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Shared\Services\MessageBus\Interfaces\QueryBus;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CollectionItemDetails implements RequestHandlerInterface
{
    private QueryBus $queryBus;

    public function __construct(
        QueryBus $queryBus,
        private array $config
    ) {
        $this->queryBus = $queryBus;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Collections::COLLECTIONS_MENU) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to list collections');
        }

        $collectionKey = $request->getAttribute('collection_key');
        $query         = new GetCollectionItemByKey($collectionKey);
        $collection    = $this->queryBus->handle($query);

        return new JsonResponse(['collection' => $collection], 200);
    }
}
