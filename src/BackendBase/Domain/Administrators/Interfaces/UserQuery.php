<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Interfaces;

use BackendBase\Domain\Administrators\Interfaces\UserId as UserIdInterface;
use BackendBase\Domain\Administrators\Model\Users;
use BackendBase\Domain\Administrators\Persistence\Doctrine\ResultObject\User;
use BackendBase\Shared\ValueObject\Interfaces\Email;
use BackendBase\Domain\Shared\DomainRepository;

interface UserQuery extends DomainRepository
{
    public function getUserById(UserIdInterface $id): ?User;

    public function getUserByEmail(Email $email): ?User;

    public function getAllUsersPaginated(int $offset, int $limit): Users;
}
