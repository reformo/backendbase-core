<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess;

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
        $app->post('/_reset', Handler\ResetApcuCache::class, 'apcu_cache.reset');
        $app->post('/sessions', Handler\StartSession::class, 'sessions.start');
        $app->get('/sessions/{token}', Handler\SessionDetails::class, 'sessions.details');

        $app->post('/users', Handler\AddUser::class, 'users.add');
        $app->get('/users', Handler\Users::class, 'users');
        $app->patch('/users/me', Handler\ChangeMyDetails::class, 'users.change_my_details');
        $app->patch('/users/{userId}', Handler\ChangeUserDetails::class, 'users.change_details');

        $app->get('/roles', Handler\UserRoles::class, 'roles');
        $app->patch('/roles/{roleId}/{action}/{type}', Handler\ChangeRolePermissions::class, 'roles.permissions.update');
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories'  => [
                Handler\ResetApcuCache::class => RequestHandlerFactory::class,
                Handler\StartSession::class => RequestHandlerFactory::class,
                Handler\SessionDetails::class => RequestHandlerFactory::class,

                Handler\AddUser::class => RequestHandlerFactory::class,
                Handler\Users::class => RequestHandlerFactory::class,
                Handler\ChangeUserDetails::class => RequestHandlerFactory::class,
                Handler\ChangeMyDetails::class => RequestHandlerFactory::class,

                Handler\UserRoles::class => RequestHandlerFactory::class,
                Handler\ChangeRolePermissions::class => RequestHandlerFactory::class,

            ],
        ];
    }
}
