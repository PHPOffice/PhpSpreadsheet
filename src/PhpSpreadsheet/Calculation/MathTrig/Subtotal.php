<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;

class Subtotal
{
    /**
     * @param mixed $cellReference
     * @param mixed $args
     */
    protected static function filterHiddenArgs($cellReference, $args): array
    {
        return array_filter(
            $args,
            function ($index) use ($cellReference) {
                [, $row, ] = explode('.', $index);

                return $cellReference->getWorksheet()->getRowDimension($row)->getVisible();
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param mixed $cellReference
     * @param mixed $args
     */
    protected static function filterFormulaArgs($cellReference, $args): array
    {
        return array_filter(
            $args,
            function ($index) use ($cellReference) {
                [, $row, $column] = explode('.', $index);
                $retVal = true;
                if ($cellReference->getWorksheet()->cellExists($column . $row)) {
                    //take this cell out if it contains the SUBTOTAL or AGGREGATE functions in a formula
                    $isFormula = $cellReference->getWorksheet()->getCell($column . $row)->isFormula();
                    $cellFormula = !preg_match('/^=.*\b(SUBTOTAL|AGGREGATE)\s*\(/i', $cellReference->getWorksheet()->getCell($column . $row)->getValue());

                    $retVal = !$isFormula || $cellFormula;
                }

                return $retVal;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /** @var callable[] */
    private const CALL_FUNCTIONS = [
        1 => [Statistical\Averages::class, 'average'],
        [Statistical\Counts::class, 'COUNT'], // 2
        [Statistical\Counts::class, 'COUNTA'], // 3
        [Statistical\Maximum::class, 'max'], // 4
        [Statistical\Minimum::class, 'min'], // 5
        [Operations::class, 'product'], // 6
        [Statistical\StandardDeviations::class, 'STDEV'], // 7
        [Statistical\StandardDeviations::class, 'STDEVP'], // 8
        [Sum::class, 'sumIgnoringStrings'], // 9
        [Statistical\Variances::class, 'VAR'], // 10
        [Statistical\Variances::class, 'VARP'], // 11
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
     *
     * @return float|string
     */
    public static function evaluate($functionType, ...$args)
    {
        $cellReference = array_pop($args);
        $aArgs = Functions::flattenArrayIndexed($args);

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
            /** @var callable */
            $call = self::CALL_FUNCTIONS[$subtotal];

            return call_user_func_array($call, $aArgs);
        }

        return Functions::VALUE();
    }
}
