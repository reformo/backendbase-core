<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_exists;
use function json_decode;

use const JSON_THROW_ON_ERROR;

class UserRoles implements RequestHandlerInterface
{
    public function __construct(private RolesRepository $rolesRepository, array $config)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * @var Role
         */
        $role = $request->getAttribute('role');
        if ($role->hasPermission(Permissions\Users::USERS_PERMISSIONS) === false) {
            throw InsufficientPrivileges::create('You dont have privilege to edit permissions');
        }

        $permissionTypes = $this->rolesRepository->getPermissionsTypesList();
        $permissions     = $this->rolesRepository->getPermissionsList();
        $rolesData       = $this->rolesRepository->getUserRoles();
        $roles           = [];
        foreach ($rolesData as $role) {
            $role['permissions'] = json_decode($role['permissions'], true, 512, JSON_THROW_ON_ERROR);
            $roles[]             = $role;
        }

        $sections = [];
        foreach ($permissionTypes as $permissionType) {
            $sections[$permissionType['slug']] = $permissionType['name'];
        }

        $permissionsTable = [];

        foreach ($permissions as $permission) {
            if (! array_key_exists($permission['type'], $permissionsTable)) {
                $permissionsTable[$permission['type']] = [
                    'title' => $sections[$permission['type']],
                    'permissions' => [],
                ];
            }

            $permissionsTable[$permission['type']]['permissions'][] = [
                'title' => $permission['name'],
                'key' => $permission['key'],
            ];
        }

        return new JsonResponse(['permissions' => $permissionsTable, 'roles' => $roles]);
    }
}
