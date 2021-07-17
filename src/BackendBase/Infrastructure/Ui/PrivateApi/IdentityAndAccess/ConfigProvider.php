<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess;

use BackendBase\Shared\Interfaces\MezzioHandlerConfigProvider;
use Mezzio\Application;
use Mezzio\MiddlewareFactory;

class ConfigProvider implements MezzioHandlerConfigProvider
{
    public function __invoke(): array
    {
        return [
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


}
