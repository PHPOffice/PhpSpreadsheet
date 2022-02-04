<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use DateTime;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date as SharedDate;
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
    const TYPE_ISO_DATE = 'd';

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
     * @param null|RichText|string $textValue Value to sanitize to an Excel string
     *
     * @return null|RichText|string Sanitized value
     */
    public static function checkString($textValue)
    {
        if ($textValue instanceof RichText) {
            // TODO: Sanitize Rich-Text string (max. character count is 32,767)
            return $textValue;
        }

        // string must never be longer than 32,767 characters, truncate if necessary
        $textValue = StringHelper::substring($textValue, 0, 32767);

        // we require that newline is represented as "\n" in core, not as "\r\n" or "\r"
        $textValue = str_replace(["\r\n", "\r"], "\n", $textValue);

        return $textValue;
    }

    /**
     * Check a value that it is a valid error code.
     *
     * @param mixed $value Value to sanitize to an Excel error code
     *
     * @return string Sanitized value
     */
    public static function checkErrorCode($value)
    {
        $value = (string) $value;

        if (!isset(self::$errorCodes[$value])) {
            $value = '#NULL!';
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return float|int
     */
    public static function checkIsoDate($value)
    {
        if (!is_string($value)) {
            throw new Exception('Non-string supplied for datatype Date');
        }

        try {
            $date = new DateTime($value);
            $newValue = SharedDate::PHPToExcel($date);
        } catch (\Exception $e) {
            throw new Exception("Invalid string $value supplied for datatype Date");
        }

        if ($newValue === false) {
            throw new Exception("Invalid string $value supplied for datatype Date");
        }

        if (preg_match('/^\\d\\d:\\d\\d:\\d\\d/', $value) == 1) {
            $newValue = fmod($newValue, 1.0);
        }

        return $newValue;
    }
}
