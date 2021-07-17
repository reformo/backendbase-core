<?php

declare(strict_types=1);

namespace BackendBase\Domain\Shared;

interface DomainEntityMapper
{
    public function toDomainEntity(array $data): DomainEntity;
}
