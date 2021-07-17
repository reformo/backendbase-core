<?php

declare(strict_types=1);

namespace BackendBase\Shared\Persistence;

interface QueryObject
{
    public function query(?array $parameters = []);
}
