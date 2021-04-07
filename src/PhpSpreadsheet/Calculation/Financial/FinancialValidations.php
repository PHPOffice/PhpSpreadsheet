<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Constants as SecuritiesConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class FinancialValidations
{
    public static function validateDate($date)
    {
        return DateTimeExcel\Helpers::getDateValue($date);
    }

    public static function validateSettlementDate($settlement)
    {
        return self::validateDate($settlement);
    }

    public static function validateMaturityDate($maturity)
    {
        return self::validateDate($maturity);
    }

    public static function validateFloat($value): float
    {
        if (!is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (float) $value;
    }

    public static function validateInt($value): int
    {
        if (!is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (int) floor($value);
    }

    public static function validateRate($rate): float
    {
        $rate = self::validateFloat($rate);
        if ($rate < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $rate;
    }

    public static function validateFrequency($frequency): int
    {
        $frequency = self::validateInt($frequency);
        if (
            ($frequency !== SecuritiesConstants::FREQUENCY_ANNUAL) &&
            ($frequency !== SecuritiesConstants::FREQUENCY_SEMI_ANNUAL) &&
            ($frequency !== SecuritiesConstants::FREQUENCY_QUARTERLY)
        ) {
            throw new Exception(Functions::NAN());
        }

        return $frequency;
    }

    public static function validateBasis($basis): int
    {
        if (!is_numeric($basis)) {
            throw new Exception(Functions::VALUE());
        }

        $basis = (int) $basis;
        if (($basis < 0) || ($basis > 4)) {
            throw new Exception(Functions::NAN());
        }

        return $basis;
    }

    public static function validatePrice($price): float
    {
        $price = self::validateFloat($price);
        if ($price < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $price;
    }

    public static function validateParValue($parValue): float
    {
        $parValue = self::validateFloat($parValue);
        if ($parValue < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $parValue;
    }

    public static function validateYield($yield): float
    {
        $yield = self::validateFloat($yield);
        if ($yield < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $yield;
    }

    public static function validateDiscount($discount): float
    {
        $discount = self::validateFloat($discount);
        if ($discount <= 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $discount;
    }
}
