<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;
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
     * Get list of error codes.
     *
     * @return array
     */
    public static function getErrorCodes()
    {
        return ExcelException::ERROR_CODES;
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
     * Check that a value is a valid error object.
     *
     * @param ExcelException|string $errorCode Value to sanitize as an Excel error object
     *
     * @return ExcelException Sanitized value
     */
    public static function checkErrorCode($errorCode): ExcelException
    {
        if ($errorCode instanceof ExcelException) {
            return $errorCode;
        }

        if (!isset(ExcelException::ERROR_CODES[$errorCode])) {
            $errorCode = ExcelException::null();
        }

        return $errorCode;
    }
}
