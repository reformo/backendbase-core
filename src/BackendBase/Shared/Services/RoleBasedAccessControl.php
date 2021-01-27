<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services;

use Laminas\Permissions\Rbac\Role;

final class RoleBasedAccessControl
{
    public static function fromPermissions(string $role, array $permissions): Role
    {
        $role = new Role($role);

        foreach ($permissions as $permission) {
            $role->addPermission($permission);
        }

        return $role;
    }
}
