<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class FinancialValidations
{
    public static function validateDate(mixed $date): float
    {
        return DateTimeExcel\Helpers::getDateValue($date);
    }

    public static function validateSettlementDate(mixed $settlement): float
    {
        return self::validateDate($settlement);
    }

    public static function validateMaturityDate(mixed $maturity): float
    {
        return self::validateDate($maturity);
    }

    public static function validateFloat(mixed $value): float
    {
        if (!is_numeric($value)) {
            throw new Exception(ExcelError::VALUE());
        }

        return (float) $value;
    }

    public static function validateInt(mixed $value): int
    {
        if (!is_numeric($value)) {
            throw new Exception(ExcelError::VALUE());
        }

        return (int) floor((float) $value);
    }

    public static function validateRate(mixed $rate): float
    {
        $rate = self::validateFloat($rate);
        if ($rate < 0.0) {
            throw new Exception(ExcelError::NAN());
        }

        return $rate;
    }

    public static function validateFrequency(mixed $frequency): int
    {
        $frequency = self::validateInt($frequency);
        if (
            ($frequency !== FinancialConstants::FREQUENCY_ANNUAL)
            && ($frequency !== FinancialConstants::FREQUENCY_SEMI_ANNUAL)
            && ($frequency !== FinancialConstants::FREQUENCY_QUARTERLY)
        ) {
            throw new Exception(ExcelError::NAN());
        }

        return $frequency;
    }

    public static function validateBasis(mixed $basis): int
    {
        if (!is_numeric($basis)) {
            throw new Exception(ExcelError::VALUE());
        }

        $basis = (int) $basis;
        if (($basis < 0) || ($basis > 4)) {
            throw new Exception(ExcelError::NAN());
        }

        return $basis;
    }

    public static function validatePrice(mixed $price): float
    {
        $price = self::validateFloat($price);
        if ($price < 0.0) {
            throw new Exception(ExcelError::NAN());
        }

        return $price;
    }

    public static function validateParValue(mixed $parValue): float
    {
        $parValue = self::validateFloat($parValue);
        if ($parValue < 0.0) {
            throw new Exception(ExcelError::NAN());
        }

        return $parValue;
    }

    public static function validateYield(mixed $yield): float
    {
        $yield = self::validateFloat($yield);
        if ($yield < 0.0) {
            throw new Exception(ExcelError::NAN());
        }

        return $yield;
    }

    public static function validateDiscount(mixed $discount): float
    {
        $discount = self::validateFloat($discount);
        if ($discount <= 0.0) {
            throw new Exception(ExcelError::NAN());
        }

        return $discount;
    }
}
