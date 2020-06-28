<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject;

use BackendBase\Shared\ValueObject\Exception\InvalidDateTimeProvided;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;

class LocalizedDateTime
{
    private DateTimeImmutable $datetime;

    private function __construct(DateTimeImmutable $datetime)
    {
        $this->datetime = $datetime;
    }

    public static function fromString(string $datetime, ?string $timezone = 'UTC') : self
    {
        $immutableDatetime =  DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            $datetime,
            new DateTimeZone($timezone)
        );
        if ($immutableDatetime === false) {
            throw InvalidDateTimeProvided::create(
                'Invalid datetime format provided. It must be: Y-m-d H:i:s',
                ['error' => 'localized-datetime/invalid-datetime-format', 'expectedFormat' => 'Y-m-d H:i:s']
            );
        }

        return new self($immutableDatetime);
    }

    public static function now(?string $timezone = 'UTC') : self
    {
        return new self(new DateTimeImmutable('now', new DateTimeZone($timezone)));
    }

    public function getDatetime() : DateTimeImmutable
    {
        return $this->datetime;
    }

    public function getTimezone() : DateTimeZone
    {
        return $this->datetime->getTimezone();
    }

    public function toFormat(string $format) : string
    {
        return $this->getDatetime()->format($format);
    }

    public function withNewTimezone(DateTimeZone $timezone) : self
    {
        return new self($this->datetime->setTimezone($timezone));
    }

    public function withAddedInterval(DateInterval $interval) : self
    {
        return new self($this->datetime->add($interval));
    }

    public function withSubtractedInterval(DateInterval $interval) : self
    {
        return new self($this->datetime->sub($interval));
    }

    public function diff(LocalizedDateTime $datetime2, ?bool $absolute = false) : DateInterval
    {
        return $this->datetime->diff($datetime2->getDatetime(), $absolute);
    }
}
