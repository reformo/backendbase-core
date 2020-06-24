<?php

declare(strict_types=1);

namespace BackendBase\Domain\Shared\Exception;

use Mezzio\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use function array_merge;
use function defined;

trait DomainException
{
    use CommonProblemDetailsExceptionTrait;

    private function __construct(int $status, string $detail, string $title, ?string $type, array $additional)
    {
        $this->status     = $status;
        $this->detail     = $detail;
        $this->title      = $title;
        $this->type       = $type;
        $this->additional = $additional;
        parent::__construct($detail, $status);
    }

    public static function create(string $details, ?array $additional = []) : self
    {
        return new static(
            self::STATUS,
            $details,
            self::TITLE,
            defined('static::TYPE') ? self::TYPE : '',
            array_merge($additional, ['code' => self::CODE])
        );
    }
}
