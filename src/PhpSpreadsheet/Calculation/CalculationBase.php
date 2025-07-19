<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

class CalculationBase
{
    /**
     * Get a list of all implemented functions as an array of function objects.
     *
     * @return array<string, array{category: string, functionCall: string|string[], argumentCount: string, passCellReference?: bool, passByReference?: bool[], custom?: bool}>
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
     * @param array{category: string, functionCall: string|string[], argumentCount: string, passCellReference?: bool, passByReference?: bool[], custom?: bool} $value
     */
    public static function addFunction(string $key, array $value): bool
    {
        $key = strtoupper($key);
        if (
            array_key_exists($key, FunctionArray::$phpSpreadsheetFunctions)
            && !self::isDummy($key)
        ) {
            return false;
        }
        $value['custom'] = true;
        FunctionArray::$phpSpreadsheetFunctions[$key] = $value;

        return true;
    }

    private static function isDummy(string $key): bool
    {
        // key is already known to exist
        $functionCall = FunctionArray::$phpSpreadsheetFunctions[$key]['functionCall'] ?? null;
        if (!is_array($functionCall)) {
            return false;
        }
        if (($functionCall[1] ?? '') !== 'DUMMY') {
            return false;
        }

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
