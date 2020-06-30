<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject\Interfaces;

interface Email
{
    public static function createFromString(string $email) : Email;
    public function getEmail() : string;
    public function toString() : string;
}
