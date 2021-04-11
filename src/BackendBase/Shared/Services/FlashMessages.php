<?php

declare(strict_types=1);

namespace BackendBase\Shared\Services;

use Redis;

use function json_decode;
use function json_encode;

use const JSON_THROW_ON_ERROR;

class FlashMessages
{
    public const FLASH_MESSAGE_ATTRIBUTE = 'flash';
    private Redis $redis;
    private string $key;

    public function __construct(Redis $redis, string $key)
    {
        $this->redis = $redis;
        $this->key   = $key;
    }

    public function flash(string $key, $value): void
    {
        $this->redis->set($this->getFlashKey($key), json_encode($value, JSON_THROW_ON_ERROR), 120);
    }

    public function getFlash(string $key)
    {
        $value =  $this->redis->get($this->getFlashKey($key));
        if (! empty($value)) {
            $this->redis->del($this->getFlashKey($key));
            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        }

        return $value;
    }

    private function getFlashKey(string $key): string
    {
        return $this->key . '-' . $key;
    }
}
