<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Constants as SecuritiesConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class FinancialValidations
{
    /**
     * @param mixed $date
     * @throws Exception
     */
    public static function validateDate($date)
    {
        return DateTimeExcel\Helpers::getDateValue($date);
    }

    /**
     * @param mixed $settlement
     * @throws Exception
     */
    public static function validateSettlementDate($settlement)
    {
        return self::validateDate($settlement);
    }

    /**
     * @param mixed $maturity
     * @throws Exception
     */
    public static function validateMaturityDate($maturity)
    {
        return self::validateDate($maturity);
    }

    /**
     * @param mixed $value
     * @throws Exception
     */
    public static function validateFloat($value): float
    {
        if (!is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (float) $value;
    }

    /**
     * @param mixed $value
     * @throws Exception
     */
    public static function validateInt($value): int
    {
        if (!is_numeric($value)) {
            throw new Exception(Functions::VALUE());
        }

        return (int) floor($value);
    }

    /**
     * @param mixed $rate
     * @throws Exception
     */
    public static function validateRate($rate): float
    {
        $rate = self::validateFloat($rate);
        if ($rate < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $rate;
    }

    /**
     * @param mixed $frequency
     * @throws Exception
     */
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

    /**
     * @param mixed $basis
     * @throws Exception
     */
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

    /**
     * @param mixed $price
     * @throws Exception
     */
    public static function validatePrice($price): float
    {
        $price = self::validateFloat($price);
        if ($price < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $price;
    }

    /**
     * @param mixed $parValue
     * @throws Exception
     */
    public static function validateParValue($parValue): float
    {
        $parValue = self::validateFloat($parValue);
        if ($parValue < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $parValue;
    }

    /**
     * @param mixed $yield
     * @throws Exception
     */
    public static function validateYield($yield): float
    {
        $yield = self::validateFloat($yield);
        if ($yield < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $yield;
    }

    /**
     * @param mixed $discount
     * @throws Exception
     */
    public static function validateDiscount($discount): float
    {
        $discount = self::validateFloat($discount);
        if ($discount <= 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $discount;
    }
}
