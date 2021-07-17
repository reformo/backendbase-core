<?php

declare(strict_types=1);

namespace BackendBase\Shared\CQRS\Interfaces;

interface Query
{
    public function getQueryName(): string;
}
