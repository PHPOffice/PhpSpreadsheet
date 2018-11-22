<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ValueBinderWithOverriddenDataTypeForValue extends DefaultValueBinder
{
    public static $called = false;

    public static function dataTypeForValue($pValue)
    {
        self::$called = true;

        return parent::dataTypeForValue($pValue);
    }
}
