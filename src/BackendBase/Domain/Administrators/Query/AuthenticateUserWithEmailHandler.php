<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Query;

use BackendBase\Domain\Administrators\Exception\UserNotFound;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetPermissionsListByRole;
use BackendBase\Domain\Administrators\Persistence\Doctrine\QueryObject\GetUserByEmail;
use BackendBase\Domain\Administrators\Persistence\Doctrine\ResultObject\User;
use BackendBase\Shared\CQRS\Interfaces\QueryHandler;

class AuthenticateUserWithEmailHandler implements QueryHandler
{
    private const CALLABLE_VERIFY_FUNCTION = 'password_verify';
    private GetUserByEmail $getUserByEmailQuery;
    private GetPermissionsListByRole $getPermissionsListByRoleQuery;

    public function __construct(GetUserByEmail $getUserByEmailQuery, GetPermissionsListByRole $getPermissionsListByRoleQuery)
    {
        $this->getUserByEmailQuery           = $getUserByEmailQuery;
        $this->getPermissionsListByRoleQuery = $getPermissionsListByRoleQuery;
    }

    public function __invoke(AuthenticateUserWithEmail $query): User
    {
        $parameters = ['email' => $query->email()];
        $user       = $this->getUserByEmailQuery->query($parameters);
        $this->verifyPassword($query->password(), $user->passwordHash(), self::CALLABLE_VERIFY_FUNCTION);
        $user->unset('passwordHash', 'passwordHashAlgo');
        $user->setPermissions($this->getPermissionsListByRoleQuery->query(['roleType' => $user->role()]));

        return $user;
    }

    private function verifyPassword(string $password, string $passwordHash): void
    {
        $verifyPasswordFunction = self::CALLABLE_VERIFY_FUNCTION;
        if ($verifyPasswordFunction($password, $passwordHash) === false) {
            throw UserNotFound::create('User not found or verfiied');
        }
    }
}
