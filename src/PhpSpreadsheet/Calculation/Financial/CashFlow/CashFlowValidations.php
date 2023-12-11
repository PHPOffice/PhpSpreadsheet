<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Constants as FinancialConstants;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\FinancialValidations;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

class CashFlowValidations extends FinancialValidations
{
    public static function validateRate(mixed $rate): float
    {
        $rate = self::validateFloat($rate);

        return $rate;
    }

    public static function validatePeriodType(mixed $type): int
    {
        $rate = self::validateInt($type);
        if (
            $type !== FinancialConstants::PAYMENT_END_OF_PERIOD
            && $type !== FinancialConstants::PAYMENT_BEGINNING_OF_PERIOD
        ) {
            throw new Exception(ExcelError::NAN());
        }

        return $rate;
    }

    public static function validatePresentValue(mixed $presentValue): float
    {
        return self::validateFloat($presentValue);
    }

    public static function validateFutureValue(mixed $futureValue): float
    {
        return self::validateFloat($futureValue);
    }
}
