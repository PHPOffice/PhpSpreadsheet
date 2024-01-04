<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls;

class ErrorCode
{
    /**
     * @var array<string, int>
     */
    protected static array $errorCodeMap = [
        '#NULL!' => 0x00,
        '#DIV/0!' => 0x07,
        '#VALUE!' => 0x0F,
        '#REF!' => 0x17,
        '#NAME?' => 0x1D,
        '#NUM!' => 0x24,
        '#N/A' => 0x2A,
    ];

    public static function error(string $errorCode): int
    {
        if (array_key_exists($errorCode, self::$errorCodeMap)) {
            return self::$errorCodeMap[$errorCode];
        }

        return 0;
    }
}
