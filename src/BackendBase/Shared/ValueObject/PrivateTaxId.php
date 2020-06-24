<?php

declare(strict_types=1);

namespace Reformo\Shared\ValueObject;

use Reformo\Shared\ValueObject\Exception\InvalidPrivateTaxIdNumber;
use function array_map;
use function str_split;
use function strlen;
use function strpos;

final class PrivateTaxId
{
    private string $taxId;

    public function __construct(string $taxId)
    {
        self::validateTaxId($taxId);
        $this->taxId = $taxId;
    }

    public function taxId() : string
    {
        return $this->taxId;
    }

    private static function validateTaxId(string $taxId) : bool
    {
        if (strpos($taxId, '0') === 0) {
            throw InvalidPrivateTaxIdNumber::create('Private tax id cannot be started with zero(0): ' . $taxId);
        }

        if (strlen($taxId) !== 11) {
            throw InvalidPrivateTaxIdNumber::create('Private tax id length must be 11: ' . $taxId);
        }

        $digits               = str_split($taxId, 1);
        $digits               = array_map('self::parseInt', $digits);
        $checksumFor10thDigit = (
                ($digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8]) * 7
                - ($digits[1] + $digits[3] + $digits[5] + $digits[7])
            ) % 10;

        $checksumFor11thDigit = ($digits[0] + $digits[1] + $digits[2] + $digits[3] + $digits[4] + $digits[5]
                + $digits[6] + $digits[7] + $digits[8] + $checksumFor10thDigit) % 10;

        if ($checksumFor10thDigit !== $digits[9]) {
            throw InvalidPrivateTaxIdNumber::create('Private tax id checksum for 10th number failed: ' . $taxId);
        }

        if ($checksumFor11thDigit !==  $digits[10]) {
            throw InvalidPrivateTaxIdNumber::create('Private tax id checksum for 11th number failed: ' . $taxId);
        }

        return true;
    }

    private static function parseInt(string $item) : int
    {
        return (int) $item;
    }
}
