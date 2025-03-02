<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

class CalculationBase
{
    /**
     * Get a list of all implemented functions as an array of function objects.
     *
     * return array<string, array<string, mixed>>
     */
    public static function getFunctions(): array
    {
        return FunctionArray::$phpSpreadsheetFunctions;
    }

    /**
     * Get address of list of all implemented functions as an array of function objects.
     *
     * @return array<string, array<string, mixed>>
     */
    protected static function &getFunctionsAddress(): array
    {
        return FunctionArray::$phpSpreadsheetFunctions;
    }

    /**
     * @param array<string, array<string, mixed>> $value
     */
    public static function addFunction(string $key, array $value): bool
    {
        $key = strtoupper($key);
        if (array_key_exists($key, FunctionArray::$phpSpreadsheetFunctions)) {
            return false;
        }
        $value['custom'] = true;
        FunctionArray::$phpSpreadsheetFunctions[$key] = $value;

        return true;
    }

    public static function removeFunction(string $key): bool
    {
        $key = strtoupper($key);
        if (array_key_exists($key, FunctionArray::$phpSpreadsheetFunctions)) {
            if (FunctionArray::$phpSpreadsheetFunctions[$key]['custom'] ?? false) {
                unset(FunctionArray::$phpSpreadsheetFunctions[$key]);

                return true;
            }
        }

        return false;
    }
}
