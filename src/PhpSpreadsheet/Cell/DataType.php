<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class DataType
{
    // Data types
    const TYPE_STRING2 = 'str';
    const TYPE_STRING = 's';
    const TYPE_FORMULA = 'f';
    const TYPE_NUMERIC = 'n';
    const TYPE_BOOL = 'b';
    const TYPE_NULL = 'null';
    const TYPE_INLINE = 'inlineStr';
    const TYPE_ERROR = 'e';

    /**
     * List of error codes.
     *
     * @var array
     */
    private static $errorCodes = [
        '#NULL!' => 0,
        '#DIV/0!' => 1,
        '#VALUE!' => 2,
        '#REF!' => 3,
        '#NAME?' => 4,
        '#NUM!' => 5,
        '#N/A' => 6,
    ];

    /**
     * Get list of error codes.
     *
     * @return array
     */
    public static function getErrorCodes()
    {
        return self::$errorCodes;
    }

    /**
     * Check a string that it satisfies Excel requirements.
     *
     * @param null|RichText|string $pValue Value to sanitize to an Excel string
     *
     * @return null|RichText|string Sanitized value
     */
    public static function checkString($pValue)
    {
        if ($pValue instanceof RichText) {
            // TODO: Sanitize Rich-Text string (max. character count is 32,767)
            return $pValue;
        }

        // string must never be longer than 32,767 characters, truncate if necessary
        $pValue = StringHelper::substring($pValue, 0, 32767);

        // we require that newline is represented as "\n" in core, not as "\r\n" or "\r"
        $pValue = str_replace(["\r\n", "\r"], "\n", $pValue);

        return $pValue;
    }

    /**
     * Check a value that it is a valid error code.
     *
     * @param mixed $pValue Value to sanitize to an Excel error code
     *
     * @return string Sanitized value
     */
    public static function checkErrorCode($pValue)
    {
        $pValue = (string) $pValue;

        if (!isset(self::$errorCodes[$pValue])) {
            $pValue = '#NULL!';
        }

        return $pValue;
    }
}
