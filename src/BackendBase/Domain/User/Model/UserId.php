<?php

declare(strict_types=1);

namespace BackendBase\Domain\User\Model;

use BackendBase\Domain\User\Interfaces\UserId as UserIdInterface;
use BackendBase\Shared\ValueObject\IdentifierTrait;

final class UserId implements UserIdInterface
{
    use IdentifierTrait;
}
