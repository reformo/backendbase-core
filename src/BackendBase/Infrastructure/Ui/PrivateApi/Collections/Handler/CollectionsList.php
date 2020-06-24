<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Collections\Handler;

use BackendBase\Domain\Collections\Query\GetCollectionItemByKey;
use BackendBase\Domain\Collections\Query\GetCollectionItems;
use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use BackendBase\Shared\Services\MessageBus\Interfaces\QueryBus;

class CollectionsList implements RequestHandlerInterface
{
    private const RESULT_ROWS_LIMIT = 100;
    private array $config;
    private QueryBus $queryBus;

    public function __construct(
        QueryBus $queryBus,
        array $config
    ) {
        $this->config   = $config;
        $this->queryBus = $queryBus;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Collections::COLLECTIONS_MENU) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to list collections');
        }
        $parentId      = null;
        $collectionKey = $request->getAttribute('collection_key', null);
        if ($collectionKey !== null) {
            $collectionItemByKeyQuery = new GetCollectionItemByKey($collectionKey);
            $collection               = $this->queryBus->handle($collectionItemByKeyQuery);
            $parentId                 = $collection->id();
        }
        $queryParams = $request->getQueryParams();
        $pageNumber  = $queryParams['pageNumber'] ?? 1;

        $query       = new GetCollectionItems($parentId, 0, self::RESULT_ROWS_LIMIT * (int) $pageNumber);
        $collections = $this->queryBus->handle($query);

        $data = ['collections' => $collections];

        return new JsonResponse($data, 200);
    }
}
