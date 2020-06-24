<?php

declare(strict_types=1);

namespace BackendBase\Shared;

use Dotenv\Dotenv;
use function getcwd;

class DotEnvConfigProvider
{
    public function __invoke() : array
    {
        $dotenv = Dotenv::create(getcwd());
        $dotenv->load();

        return [];
    }
}
