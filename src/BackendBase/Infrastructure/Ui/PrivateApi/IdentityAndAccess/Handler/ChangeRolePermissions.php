<?php

declare(strict_types=1);

namespace BackendBase\PrivateApi\IdentityAndAccess\Handler;

use BackendBase\Domain\IdentityAndAccess\Exception\InsufficientPrivileges;
use BackendBase\Domain\IdentityAndAccess\Model\Permissions;
use BackendBase\Infrastructure\Persistence\Doctrine\Entity\UserRole;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\GenericRepository;
use BackendBase\Infrastructure\Persistence\Doctrine\Repository\RolesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Permissions\Rbac\Role;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;

class ChangeRolePermissions implements RequestHandlerInterface
{
    public function __construct(private RolesRepository $rolesRepository, private GenericRepository $genericRepository, array $config)
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

        $roleId = $request->getAttribute('roleId');
        $action = $request->getAttribute('action');
        $type   = $request->getAttribute('type');
        if (! in_array($action, ['add', 'remove'])) {
            throw InsufficientPrivileges::create('You dont have privilege to edit permissions');
        }

        $userRole    = $this->genericRepository->findGeneric(UserRole::class, $roleId);
        $permissions = new ArrayCollection($userRole->permissions());
        if ($action === 'add' && $permissions->indexOf($type) === false) {
            $permissions->add($type);
        }

        if ($action === 'remove') {
            $permissions->removeElement($type);
        }

        $userRole->setPermissions($permissions->getValues());
        $this->genericRepository->persistGeneric($userRole);

        return new EmptyResponse();
    }
}
