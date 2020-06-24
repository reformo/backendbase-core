<?php

declare(strict_types=1);

namespace Reformo\Shared\ValueObject;

use Reformo\Shared\ValueObject\Exception\InvalidCorporateTaxIdNumber;
use function array_map;
use function count;
use function pow;
use function str_split;
use function strlen;

final class CorporateTaxId
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
        if (strlen($taxId) !== 10) {
            throw InvalidCorporateTaxIdNumber::create('Corporate tax id length must be 10: ' . $taxId);
        }

        $digits = str_split($taxId, 1);
        $digits = array_map('self::parseInt', $digits);
        $sum    = 0;
        for ($i=0; $i<count($digits)-1; $i++) {
            $temp = ($digits[$i] + 10 - ($i+1)) % 10;
            $incr = ($temp === 9 ? 9 : ($temp * pow(2, 10 - ($i+1))) % 9);
            $sum += $incr;
        }

        $checksum = (10 - $sum % 10) % 10;
        if ($checksum !== $digits[9]) {
            throw InvalidCorporateTaxIdNumber::create('Corporate tax id checksum for 10th number failed: ' . $taxId);
        }

        return true;
    }

    private static function parseInt(string $item) : int
    {
        return (int) $item;
    }
}
