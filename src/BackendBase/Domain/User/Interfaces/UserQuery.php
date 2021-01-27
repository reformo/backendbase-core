<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Interfaces;

use BackendBase\Domain\User\Interfaces\UserId as UserIdInterface;
use BackendBase\Domain\User\Model\Users;
use BackendBase\Domain\User\Persistence\Doctrine\ResultObject\User;
use BackendBase\Shared\ValueObject\Interfaces\Email;

interface UserQuery
{
    public function getUserById(UserIdInterface $id): ?User;

    public function getUserByEmail(Email $email): ?User;

    public function getAllUsersPaginated(int $offset, int $limit): Users;
}
