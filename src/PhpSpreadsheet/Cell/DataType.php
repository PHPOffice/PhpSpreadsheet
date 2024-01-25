<?php

namespace PhpOffice\PhpSpreadsheet\Cell;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class DataType
{
    // Data types
    public const TYPE_STRING2 = 'str';
    public const TYPE_STRING = 's';
    public const TYPE_FORMULA = 'f';
    public const TYPE_NUMERIC = 'n';
    public const TYPE_BOOL = 'b';
    public const TYPE_NULL = 'null';
    public const TYPE_INLINE = 'inlineStr';
    public const TYPE_ERROR = 'e';
    public const TYPE_ISO_DATE = 'd';

    /**
     * List of error codes.
     *
     * @var array<string, int>
     */
    private static array $errorCodes = [
        '#NULL!' => 0,
        '#DIV/0!' => 1,
        '#VALUE!' => 2,
        '#REF!' => 3,
        '#NAME?' => 4,
        '#NUM!' => 5,
        '#N/A' => 6,
        '#CALC!' => 7,
    ];

    public const MAX_STRING_LENGTH = 32767;

    /**
     * Get list of error codes.
     *
     * @return array<string, int>
     */
    public static function getErrorCodes(): array
    {
        return self::$errorCodes;
    }

    /**
     * Check a string that it satisfies Excel requirements.
     *
     * @param null|RichText|string $textValue Value to sanitize to an Excel string
     *
     * @return RichText|string Sanitized value
     */
    public static function checkString(null|RichText|string $textValue): RichText|string
    {
        if ($textValue instanceof RichText) {
            // TODO: Sanitize Rich-Text string (max. character count is 32,767)
            return $textValue;
        }

        // string must never be longer than 32,767 characters, truncate if necessary
        $textValue = StringHelper::substring((string) $textValue, 0, self::MAX_STRING_LENGTH);

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
    public static function checkErrorCode(mixed $value): string
    {
        $value = (string) $value;

        if (!isset(self::$errorCodes[$value])) {
            $value = '#NULL!';
        }

        return $value;
    }
}
