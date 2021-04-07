<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\FinancialValidations;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Constants;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;

class CashFlowValidations extends FinancialValidations
{
    public static function validateRate($rate): float
    {
        $rate = self::validateFloat($rate);
        if ($rate < 0.0) {
            throw new Exception(Functions::NAN());
        }

        return $rate;
    }

    public static function validatePeriodType($type): int
    {
        $rate = self::validateInt($type);
        if ($type !== Constants::END_OF_PERIOD && $type !== Constants::BEGINNING_OF_PERIOD) {
            throw new Exception(Functions::NAN());
        }

        return $rate;
    }

    public static function validatePresentValue($presentValue): float
    {
        return self::validateFloat($presentValue);
    }

    public static function validateFutureValue($presentValue): float
    {
        return self::validateFloat($presentValue);
    }
}
