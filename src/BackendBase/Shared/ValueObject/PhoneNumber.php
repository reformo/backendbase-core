<?php

declare(strict_types=1);

namespace BackendBase\Shared\ValueObject;

use InvalidArgumentException;
use BackendBase\Shared\ValueObject\Exception\InvalidPhoneNumber;
use Webmozart\Assert\Assert;
use function preg_replace;
use function str_replace;
use function strlen;
use function strpos;
use function substr;
use function trim;

final class PhoneNumber
{
    private string $countryCode;
    private string $areaCode;
    private string $phoneNumber;

    public function __construct(string $countryCode, string $areaCode, string $phoneNumber)
    {
        $phoneNumber =  preg_replace('/\s+/', '', trim($phoneNumber));
        try {
            Assert::minLength($countryCode, 1);
            Assert::numeric($countryCode);
        } catch (InvalidArgumentException $e) {
            throw InvalidPhoneNumber::create(
                'Invalid country code provided.',
                ['error' => 'phone-number/invalid-country-code']
            );
        }

        try {
            Assert::minLength($areaCode, 3);
            Assert::numeric($areaCode);
        } catch (InvalidArgumentException $e) {
            throw InvalidPhoneNumber::create(
                'Area code length must be 3 characters long using numbers',
                ['error' => 'phone-number/invalid-area-code']
            );
        }

        try {
            Assert::minLength($phoneNumber, 7);
            Assert::numeric($phoneNumber);
        } catch (InvalidArgumentException $e) {
            throw InvalidPhoneNumber::create(
                'Phone number length must be 7 characters long using numbers',
                ['error' => 'phone-number/invalid-phone-number']
            );
        }

        $this->countryCode = $countryCode;
        $this->areaCode    = $areaCode;
        $this->phoneNumber = $phoneNumber;
    }

    public static function fromString(string $e164FormattedPhoneNumber) : self
    {
        $e164FormattedPhoneNumber = preg_replace('/\s+/', '', trim($e164FormattedPhoneNumber));
        if (strpos($e164FormattedPhoneNumber, '+') !== 0) {
            throw InvalidPhoneNumber::create(
                'Phone number must be start with "+".',
                ['error' => 'phone-number/must-be-start-with-plus']
            );
        }

        $e164FormattedPhoneNumber = str_replace('+', '', $e164FormattedPhoneNumber);
        $phoneNumber              = substr($e164FormattedPhoneNumber, strlen($e164FormattedPhoneNumber)-7, 7);
        $areaCode                 = substr($e164FormattedPhoneNumber, strlen($e164FormattedPhoneNumber)-10, 3);
        $countryCode              =  str_replace([$phoneNumber, $areaCode], '', $e164FormattedPhoneNumber);

        return new self($countryCode, $areaCode, $phoneNumber);
    }

    public function getCountryCode() : string
    {
        return $this->countryCode;
    }

    public function getAreaCode() : string
    {
        return $this->areaCode;
    }

    public function getPhoneNumber() : string
    {
        return $this->phoneNumber;
    }

    public function getE164FormattedNumber() : string
    {
        return '+' . $this->countryCode . $this->areaCode . $this->phoneNumber;
    }
}
