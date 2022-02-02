<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Internal;

use PhpOffice\PhpSpreadsheet\Cell\Cell;

class ExcelArrayPseudoFunctions
{
    public static function single(string $cellReference, ?Cell $cell): array
    {
        return [[$cellReference]];
    }

    public static function anchorArray(string $cellReference, ?Cell $cell): array
    {
        return [[$cellReference]];
    }
}
