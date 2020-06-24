<?php

declare(strict_types=1);

namespace BackendBase\Domain\IdentityAndAccess\Model;

class Login
{
    public const LOGIN_ATTEMPT_LIMIT    = 10;
    public const RATE_LIMIT_WINDOW      = 'perHour';
    public const RATE_LIMIT_WINDOW_DESC = 'per hour';
}
