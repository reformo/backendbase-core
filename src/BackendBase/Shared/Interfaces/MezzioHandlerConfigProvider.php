<?php

declare(strict_types=1);

namespace BackendBase\Shared\Interfaces;

use Mezzio\Application;
use Mezzio\MiddlewareFactory;

interface MezzioHandlerConfigProvider
{
    public function registerRoutes(Application $app, MiddlewareFactory $factory) : void;

    public function getDependencies() : array;
}
