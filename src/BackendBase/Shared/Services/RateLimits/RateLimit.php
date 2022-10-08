<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services\RateLimits;

use RateLimit\RateLimiter;
use RateLimit\RedisRateLimiter;
abstract class RateLimit implements RateLimiter
{
    private function __construct(private RateLimiter $redisRateLimiter){
    }

    public static function create(RedisRateLimiter $redisRateLimiter) {
        return new static($redisRateLimiter);
    }
    public function limit(string $identifier): void
    {
       $this->redisRateLimiter->limit($identifier);
    }
}