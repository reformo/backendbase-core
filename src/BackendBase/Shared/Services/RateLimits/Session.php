<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services\RateLimits;

class Session extends RateLimit
{
    public const RATE_LIMIT_COUNT       = 100;
    public const RATE_LIMIT_WINDOW      = 'perHour';
    public const RATE_LIMIT_WINDOW_DESC = 'per hour';
}