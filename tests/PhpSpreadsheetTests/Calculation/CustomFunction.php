<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcException;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Helpers;

class CustomFunction
{
    public static function fourthPower(mixed $number): float|int|string
    {
        try {
            $number = Helpers::validateNumericNullBool($number);
        } catch (CalcException $e) {
            return $e->getMessage();
        }

        return $number ** 4;
    }
}
