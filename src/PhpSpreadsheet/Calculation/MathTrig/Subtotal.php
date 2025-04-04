<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PhpOffice\PhpSpreadsheet\Cell\Cell;

class Subtotal
{
    protected static function filterHiddenArgs(Cell $cellReference, array $args): array
    {
        return array_filter(
            $args,
            function ($index) use ($cellReference) {
                $explodeArray = explode('.', $index);
                $row = $explodeArray[1] ?? '';
                if (!is_numeric($row)) {
                    return true;
                }

                return $cellReference->getWorksheet()->getRowDimension((int) $row)->getVisible();
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    protected static function filterFormulaArgs(Cell $cellReference, array $args): array
    {
        return array_filter(
            $args,
            function ($index) use ($cellReference): bool {
                $explodeArray = explode('.', $index);
                $row = $explodeArray[1] ?? '';
                $column = $explodeArray[2] ?? '';
                $retVal = true;
                if ($cellReference->getWorksheet()->cellExists($column . $row)) {
                    //take this cell out if it contains the SUBTOTAL or AGGREGATE functions in a formula
                    $isFormula = $cellReference->getWorksheet()->getCell($column . $row)->isFormula();
                    $cellFormula = !preg_match(
                        '/^=.*\b(SUBTOTAL|AGGREGATE)\s*\(/i',
                        $cellReference->getWorksheet()->getCell($column . $row)->getValueString()
                    );

                    $retVal = !$isFormula || $cellFormula;
                }

                return $retVal;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @var array<int, callable>
     */
    private const CALL_FUNCTIONS = [
        1 => [Statistical\Averages::class, 'average'], // 1 and 101
        [Statistical\Counts::class, 'COUNT'], // 2 and 102
        [Statistical\Counts::class, 'COUNTA'], // 3 and 103
        [Statistical\Maximum::class, 'max'], // 4 and 104
        [Statistical\Minimum::class, 'min'], // 5 and 105
        [Operations::class, 'product'], // 6 and 106
        [Statistical\StandardDeviations::class, 'STDEV'], // 7 and 107
        [Statistical\StandardDeviations::class, 'STDEVP'], // 8 and 108
        [Sum::class, 'sumIgnoringStrings'], // 9 and 109
        [Statistical\Variances::class, 'VAR'], // 10 and 110
        [Statistical\Variances::class, 'VARP'], // 111 and 111
    ];

    /**
     * SUBTOTAL.
     *
     * Returns a subtotal in a list or database.
     *
     * @param mixed $functionType
     *            A number 1 to 11 that specifies which function to
     *                    use in calculating subtotals within a range
     *                    list
     *            Numbers 101 to 111 shadow the functions of 1 to 11
     *                    but ignore any values in the range that are
     *                    in hidden rows
     * @param mixed[] $args A mixed data series of values
     */
    public static function evaluate(mixed $functionType, ...$args): float|int|string
    {
        /** @var Cell */
        $cellReference = array_pop($args);
        $bArgs = Functions::flattenArrayIndexed($args);
        $aArgs = [];
        // int keys must come before string keys for PHP 8.0+
        // Otherwise, PHP thinks positional args follow keyword
        //    in the subsequent call to call_user_func_array.
        // Fortunately, order of args is unimportant to Subtotal.
        foreach ($bArgs as $key => $value) {
            if (is_int($key)) {
                $aArgs[$key] = $value;
            }
        }
        foreach ($bArgs as $key => $value) {
            if (!is_int($key)) {
                $aArgs[$key] = $value;
            }
        }

        try {
            $subtotal = (int) Helpers::validateNumericNullBool($functionType);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        // Calculate
        if ($subtotal > 100) {
            $aArgs = self::filterHiddenArgs($cellReference, $aArgs);
            $subtotal -= 100;
        }

        $aArgs = self::filterFormulaArgs($cellReference, $aArgs);
        if (array_key_exists($subtotal, self::CALL_FUNCTIONS)) {
            $call = self::CALL_FUNCTIONS[$subtotal];

            return call_user_func_array($call, $aArgs); //* @phpstan-ignore-line
        }

        return ExcelError::VALUE();
    }
}
