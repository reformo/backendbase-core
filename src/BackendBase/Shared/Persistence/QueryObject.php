<?php

declare(strict_types=1);

namespace BackendBase\Shared\Persistence;

use BackendBase\Shared\Persistence\Doctrine\ResultObject;

interface QueryObject
{
    public function query(array |null $parameters = []): ResultObject|array|null;
}
