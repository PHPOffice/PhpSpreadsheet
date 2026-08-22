<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ValueBinderWithOverriddenDataTypeForValue extends DefaultValueBinder
{
    public static bool $called = false;

    public static function dataTypeForValue(mixed $value): string
    {
        self::$called = true;

        return parent::dataTypeForValue($value);
    }
}
