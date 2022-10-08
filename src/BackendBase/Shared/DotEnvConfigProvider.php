<?php

declare(strict_types=1);

namespace BackendBase\Shared;

use Dotenv\Dotenv;
use Dotenv\Repository\RepositoryBuilder;
use Dotenv\Repository\Adapter\PutenvAdapter;

use function getcwd;

class DotEnvConfigProvider
{
    public function __invoke(): array
    {
        $repository = RepositoryBuilder::createWithNoAdapters()
            ->addAdapter(PutenvAdapter::class)
            ->immutable()
            ->make();
        $dotenv = Dotenv::create($repository, getcwd());
        $dotenv->load();
        return [];
    }
}
