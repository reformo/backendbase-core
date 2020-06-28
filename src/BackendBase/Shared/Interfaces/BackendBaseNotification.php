<?php

declare(strict_types=1);

namespace BackendBase\Shared\Interfaces;

interface BackendBaseNotification
{
    public function send(string $sender, array $recipients, string $subject, string $body, ?array $additionalData = []) : bool;
}
