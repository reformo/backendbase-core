<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Interfaces;

use BackendBase\Domain\Administrators\Exception\UserNotFound;
use BackendBase\Domain\Administrators\Model\User;
use BackendBase\Domain\Shared\DomainRepository;
use BackendBase\Domain\Shared\Exception\ExecutionFailed;
use BackendBase\Shared\ValueObject\Email;

interface UserRepository extends DomainRepository
{
    public function getUserById(UserId $userId): ?User;

    public function getUserByEmail(Email $email): ?User;

    public function registerUser(User $user): void;

    /**
     * @throws UserNotFound
     * @throws ExecutionFailed
     *
     * @var UserId
     */
    public function unregisterUser(UserId $id): void;

    public function updateUserInfo(UserId $userId, array $payload): void;
}
