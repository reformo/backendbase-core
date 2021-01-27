<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\Collections;

use BackendBase\Shared\Factory\RequestHandlerFactory;
use BackendBase\Shared\Interfaces\MezzioHandlerConfigProvider;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider implements MezzioHandlerConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies'  => $this->getDependencies(),
        ];
    }

    public function registerRoutes(Application $app, MiddlewareFactory $factory): void
    {
        $app->get('/lookup-table', Handler\CollectionsData::class);
        $app->get('/collections', Handler\CollectionsList::class);
        $app->post('/collections', Handler\AddNewCollectionItem::class);
        $app->get('/collections/{collection_key}', Handler\CollectionItemDetails::class);
        $app->patch('/collections/{collectionId}', Handler\UpdateCollectionItem::class);
        $app->delete('/collections/{collectionId}', Handler\RemoveCollectionItem::class);
        $app->get('/collections/{collection_key}/items', Handler\CollectionsList::class);
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories'  => [
                Handler\CollectionsData::class => RequestHandlerFactory::class,
                Handler\CollectionsList::class => RequestHandlerFactory::class,
                Handler\AddNewCollectionItem::class => RequestHandlerFactory::class,
                Handler\CollectionItemDetails::class => RequestHandlerFactory::class,
                Handler\UpdateCollectionItem::class => RequestHandlerFactory::class,
                Handler\RemoveCollectionItem::class => RequestHandlerFactory::class,
            ],
        ];
    }
}
