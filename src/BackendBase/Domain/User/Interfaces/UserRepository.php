<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Interfaces;

use BackendBase\Domain\User\Exception\UserNotFound;
use BackendBase\Domain\User\Model\User;
use BackendBase\Shared\Exception\ExecutionFailed;
use BackendBase\Shared\ValueObject\Email;

interface UserRepository
{
    public function getUserById(UserId $id) : ?User;

    public function getUserByEmail(Email $email) : ?User;

    public function registerUser(User $user) : void;

    /**
     * @throws UserNotFound
     * @throws ExecutionFailed
     *
     * @var UserId
     */
    public function unregisterUser(UserId $id) : void;

    public function updateUserInfo(UserId $userId, array $payload) : void;
}
