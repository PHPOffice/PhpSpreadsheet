<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Address
{
    /**
     * ADDRESS.
     *
     * Creates a cell address as text, given specified row and column numbers.
     *
     * Excel Function:
     *        =ADDRESS(row, column, [relativity], [referenceStyle], [sheetText])
     *
     * @Deprecated 1.18.0
     *
     * @see Use the address() method in the LookupRef\Cell class instead
     *
     * @param mixed $row Row number to use in the cell reference
     * @param mixed $column Column number to use in the cell reference
     * @param int $relativity Flag indicating the type of reference to return
     *                                1 or omitted     Absolute
     *                                2                Absolute row; relative column
     *                                3                Relative row; absolute column
     *                                4                Relative
     * @param bool $referenceStyle A logical value that specifies the A1 or R1C1 reference style.
     *                                TRUE or omitted      ADDRESS returns an A1-style reference
     *                                FALSE                ADDRESS returns an R1C1-style reference
     * @param string $sheetText Optional Name of worksheet to use
     *
     * @return string
     */
    public static function cell($row, $column, $relativity = 1, $referenceStyle = true, $sheetText = '')
    {
        $row = Functions::flattenSingleValue($row);
        $column = Functions::flattenSingleValue($column);
        $relativity = Functions::flattenSingleValue($relativity);
        $sheetText = Functions::flattenSingleValue($sheetText);

        if (($row < 1) || ($column < 1)) {
            return Functions::VALUE();
        }

        if ($sheetText > '') {
            if (strpos($sheetText, ' ') !== false || strpos($sheetText, '[') !== false) {
                $sheetText = "'{$sheetText}'";
            }
            $sheetText .= '!';
        }
        if ((!is_bool($referenceStyle)) || $referenceStyle) {
            $rowRelative = $columnRelative = '$';
            $column = Coordinate::stringFromColumnIndex($column);
            if (($relativity == 2) || ($relativity == 4)) {
                $columnRelative = '';
            }
            if (($relativity == 3) || ($relativity == 4)) {
                $rowRelative = '';
            }

            return "{$sheetText}{$columnRelative}{$column}{$rowRelative}{$row}";
        }
        if (($relativity == 2) || ($relativity == 4)) {
            $column = "[{$column}]";
        }
        if (($relativity == 3) || ($relativity == 4)) {
            $row = "[{$row}]";
        }

        return "{$sheetText}R{$row}C{$column}";
    }
}
