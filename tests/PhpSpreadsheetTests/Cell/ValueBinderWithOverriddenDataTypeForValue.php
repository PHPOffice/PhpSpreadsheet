<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ValueBinderWithOverriddenDataTypeForValue extends DefaultValueBinder
{
    /**
     * @var bool
     */
    public static $called = false;

    public static function dataTypeForValue($value)
    {
        self::$called = true;

        return parent::dataTypeForValue($value);
    }
}
