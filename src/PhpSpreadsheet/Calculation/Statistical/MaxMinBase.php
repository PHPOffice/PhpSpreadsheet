<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ErrorValue;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

abstract class MaxMinBase
{
    protected static function datatypeAdjustmentAllowStrings(int|float|string|bool $value): int|float
    {
        if (is_bool($value)) {
            return (int) $value;
        } elseif (is_string($value)) {
            return 0;
        }

        return $value;
    }

    /**
     * As in Excel (and SUM), a logical value or a text representation of a number
     * typed directly into the argument list counts, and any other text typed there is #VALUE!.
     * References and arrays are left unchanged, so that their non-numeric values are ignored.
     *
     * @param mixed[] $args
     *
     * @return mixed[]
     */
    protected static function datatypeAdjustmentLiteralArguments(array $args): array
    {
        foreach ($args as $index => $arg) {
            if (!is_array($arg) && !Functions::isCellValue($index)) {
                $args[$index] = self::datatypeAdjustmentLiteral($arg);
            }
        }

        return $args;
    }

    private static function datatypeAdjustmentLiteral(mixed $value): mixed
    {
        if (is_bool($value)) {
            return (int) $value;
        }
        if (is_string($value) && !ErrorValue::isError($value, true)) {
            return is_numeric($value) ? 0 + $value : ExcelError::VALUE();
        }

        return $value;
    }
}
