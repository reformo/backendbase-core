<?php

declare(strict_types=1);

namespace BackendBase\Domain\Administrators\Model;

use BackendBase\Domain\Administrators\Interfaces\UserId as UserIdInterface;
use BackendBase\Shared\ValueObject\IdentifierTrait;

final class UserId implements UserIdInterface
{
    use IdentifierTrait;
}
