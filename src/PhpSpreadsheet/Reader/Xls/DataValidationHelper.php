<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class DataValidationHelper
{
    /**
     * @var array<int, string>
     */
    private static array $types = [
        0x00 => DataValidation::TYPE_NONE,
        0x01 => DataValidation::TYPE_WHOLE,
        0x02 => DataValidation::TYPE_DECIMAL,
        0x03 => DataValidation::TYPE_LIST,
        0x04 => DataValidation::TYPE_DATE,
        0x05 => DataValidation::TYPE_TIME,
        0x06 => DataValidation::TYPE_TEXTLENGTH,
        0x07 => DataValidation::TYPE_CUSTOM,
    ];

    /**
     * @var array<int, string>
     */
    private static array $errorStyles = [
        0x00 => DataValidation::STYLE_STOP,
        0x01 => DataValidation::STYLE_WARNING,
        0x02 => DataValidation::STYLE_INFORMATION,
    ];

    /**
     * @var array<int, string>
     */
    private static array $operators = [
        0x00 => DataValidation::OPERATOR_BETWEEN,
        0x01 => DataValidation::OPERATOR_NOTBETWEEN,
        0x02 => DataValidation::OPERATOR_EQUAL,
        0x03 => DataValidation::OPERATOR_NOTEQUAL,
        0x04 => DataValidation::OPERATOR_GREATERTHAN,
        0x05 => DataValidation::OPERATOR_LESSTHAN,
        0x06 => DataValidation::OPERATOR_GREATERTHANOREQUAL,
        0x07 => DataValidation::OPERATOR_LESSTHANOREQUAL,
    ];

    public static function type(int $type): ?string
    {
        if (isset(self::$types[$type])) {
            return self::$types[$type];
        }

        return null;
    }

    public static function errorStyle(int $errorStyle): ?string
    {
        if (isset(self::$errorStyles[$errorStyle])) {
            return self::$errorStyles[$errorStyle];
        }

        return null;
    }

    public static function operator(int $operator): ?string
    {
        if (isset(self::$operators[$operator])) {
            return self::$operators[$operator];
        }

        return null;
    }
}
