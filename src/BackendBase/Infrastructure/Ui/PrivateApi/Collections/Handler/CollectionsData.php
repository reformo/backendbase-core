<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Collections\Handler;

use BackendBase\Domain\Collections\Interfaces\CollectionQuery;
use BackendBase\Shared\Services\MessageBus\Interfaces\QueryBus;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CollectionsData implements RequestHandlerInterface
{
    private const RESULT_ROWS_LIMIT = 25;
    private QueryBus $queryBus;

    public function __construct(
        QueryBus $queryBus,
        private CollectionQuery $collectionQuery,
        private array $config
    ) {
        $this->queryBus        = $queryBus;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $collections         = $this->collectionQuery->buildLookupData();
        $collectionsWithKeys = [];
        foreach ($collections as $collection) {
            $collectionsWithKeys[$collection['id']] = $collection;
        }

        $lookupData = $this->collectionQuery->buildLookupTable();

        $data = ['collections' => $collectionsWithKeys, 'lookupTable' => $lookupData];

        return new JsonResponse($data, 200);
    }
}
