<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Style\Conditional;

class ConditionalFormatting
{
    /**
     * @var array<int, string>
     */
    private static array $types = [
        0x01 => Conditional::CONDITION_CELLIS,
        0x02 => Conditional::CONDITION_EXPRESSION,
    ];

    /**
     * @var array<int, string>
     */
    private static array $operators = [
        0x00 => Conditional::OPERATOR_NONE,
        0x01 => Conditional::OPERATOR_BETWEEN,
        0x02 => Conditional::OPERATOR_NOTBETWEEN,
        0x03 => Conditional::OPERATOR_EQUAL,
        0x04 => Conditional::OPERATOR_NOTEQUAL,
        0x05 => Conditional::OPERATOR_GREATERTHAN,
        0x06 => Conditional::OPERATOR_LESSTHAN,
        0x07 => Conditional::OPERATOR_GREATERTHANOREQUAL,
        0x08 => Conditional::OPERATOR_LESSTHANOREQUAL,
    ];

    public static function type(int $type): ?string
    {
        if (isset(self::$types[$type])) {
            return self::$types[$type];
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
