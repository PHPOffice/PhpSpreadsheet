<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class Biff8 extends Xls
{
    /**
     * read BIFF8 constant value array from array data
     * returns e.g. ['value' => '{1,2;3,4}', 'size' => 40]
     * section 2.5.8.
     *
     * @return array{value: string, size: int}
     */
    protected static function readBIFF8ConstantArray(string $arrayData): array
    {
        // offset: 0; size: 1; number of columns decreased by 1
        $nc = ord($arrayData[0]);

        // offset: 1; size: 2; number of rows decreased by 1
        $nr = self::getUInt2d($arrayData, 1);
        $size = 3; // initialize
        $arrayData = substr($arrayData, 3);

        // offset: 3; size: var; list of ($nc + 1) * ($nr + 1) constant values
        $matrixChunks = [];
        for ($r = 1; $r <= $nr + 1; ++$r) {
            $items = [];
            for ($c = 1; $c <= $nc + 1; ++$c) {
                $constant = self::readBIFF8Constant($arrayData);
                $items[] = $constant['value'];
                $arrayData = substr($arrayData, $constant['size']);
                $size += $constant['size'];
            }
            $matrixChunks[] = implode(',', $items); // looks like e.g. '1,"hello"'
        }
        $matrix = '{' . implode(';', $matrixChunks) . '}';

        return [
            'value' => $matrix,
            'size' => $size,
        ];
    }

    /**
     * read BIFF8 constant value which may be 'Empty Value', 'Number', 'String Value', 'Boolean Value', 'Error Value'
     * section 2.5.7
     * returns e.g. ['value' => '5', 'size' => 9].
     *
     * @return array{value: bool|float|int|string, size: int}
     */
    private static function readBIFF8Constant(string $valueData): array
    {
        // offset: 0; size: 1; identifier for type of constant
        $identifier = ord($valueData[0]);

        switch ($identifier) {
            case 0x00: // empty constant (what is this?)
                $value = '';
                $size = 9;

                break;
            case 0x01: // number
                // offset: 1; size: 8; IEEE 754 floating-point value
                $value = self::extractNumber(substr($valueData, 1, 8));
                $size = 9;

                break;
            case 0x02: // string value
                // offset: 1; size: var; Unicode string, 16-bit string length
                $string = self::readUnicodeStringLong(substr($valueData, 1));
                $value = '"' . $string['value'] . '"';
                $size = 1 + $string['size'];

                break;
            case 0x04: // boolean
                // offset: 1; size: 1; 0 = FALSE, 1 = TRUE
                if (ord($valueData[1])) {
                    $value = 'TRUE';
                } else {
                    $value = 'FALSE';
                }
                $size = 9;

                break;
            case 0x10: // error code
                // offset: 1; size: 1; error code
                $value = ErrorCode::lookup(ord($valueData[1]));
                $size = 9;

                break;
            default:
                throw new ReaderException('Unsupported BIFF8 constant');
        }

        return [
            'value' => $value,
            'size' => $size,
        ];
    }

    /**
     * Read BIFF8 cell range address list
     * section 2.5.15.
     *
     * @return array{size: int, cellRangeAddresses: mixed[]}
     */
    public static function readBIFF8CellRangeAddressList(string $subData): array
    {
        $cellRangeAddresses = [];

        // offset: 0; size: 2; number of the following cell range addresses
        $nm = self::getUInt2d($subData, 0);

        $offset = 2;
        // offset: 2; size: 8 * $nm; list of $nm (fixed) cell range addresses
        for ($i = 0; $i < $nm; ++$i) {
            $cellRangeAddresses[] = self::readBIFF8CellRangeAddressFixed(substr($subData, $offset, 8));
            $offset += 8;
        }

        return [
            'size' => 2 + 8 * $nm,
            'cellRangeAddresses' => $cellRangeAddresses,
        ];
    }

    /**
     * Reads a cell address in BIFF8 e.g. 'A2' or '$A$2'
     * section 3.3.4.
     */
    protected static function readBIFF8CellAddress(string $cellAddressStructure): string
    {
        // offset: 0; size: 2; index to row (0... 65535) (or offset (-32768... 32767))
        $row = self::getUInt2d($cellAddressStructure, 0) + 1;

        // offset: 2; size: 2; index to column or column offset + relative flags
        // bit: 7-0; mask 0x00FF; column index
        $column = Coordinate::stringFromColumnIndex((0x00FF & self::getUInt2d($cellAddressStructure, 2)) + 1);

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getUInt2d($cellAddressStructure, 2))) {
            $column = '$' . $column;
        }
        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getUInt2d($cellAddressStructure, 2))) {
            $row = '$' . $row;
        }

        return $column . $row;
    }

    /**
     * Reads a cell address in BIFF8 for shared formulas. Uses positive and negative values for row and column
     * to indicate offsets from a base cell
     * section 3.3.4.
     *
     * @param string $baseCell Base cell, only needed when formula contains tRefN tokens, e.g. with shared formulas
     */
    protected static function readBIFF8CellAddressB(string $cellAddressStructure, string $baseCell = 'A1'): string
    {
        [$baseCol, $baseRow] = Coordinate::coordinateFromString($baseCell);
        $baseCol = Coordinate::columnIndexFromString($baseCol) - 1;
        $baseRow = (int) $baseRow;

        // offset: 0; size: 2; index to row (0... 65535) (or offset (-32768... 32767))
        $rowIndex = self::getUInt2d($cellAddressStructure, 0);
        $row = self::getUInt2d($cellAddressStructure, 0) + 1;

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getUInt2d($cellAddressStructure, 2))) {
            // offset: 2; size: 2; index to column or column offset + relative flags
            // bit: 7-0; mask 0x00FF; column index
            $colIndex = 0x00FF & self::getUInt2d($cellAddressStructure, 2);

            $column = Coordinate::stringFromColumnIndex($colIndex + 1);
            $column = '$' . $column;
        } else {
            // offset: 2; size: 2; index to column or column offset + relative flags
            // bit: 7-0; mask 0x00FF; column index
            $relativeColIndex = 0x00FF & self::getInt2d($cellAddressStructure, 2);
            $colIndex = $baseCol + $relativeColIndex;
            $colIndex = ($colIndex < 256) ? $colIndex : $colIndex - 256;
            $colIndex = ($colIndex >= 0) ? $colIndex : $colIndex + 256;
            $column = Coordinate::stringFromColumnIndex($colIndex + 1);
        }

        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getUInt2d($cellAddressStructure, 2))) {
            $row = '$' . $row;
        } else {
            $rowIndex = ($rowIndex <= 32767) ? $rowIndex : $rowIndex - 65536;
            $row = $baseRow + $rowIndex;
        }

        return $column . $row;
    }

    /**
     * Reads a cell range address in BIFF8 e.g. 'A2:B6' or 'A1'
     * always fixed range
     * section 2.5.14.
     */
    protected static function readBIFF8CellRangeAddressFixed(string $subData): string
    {
        // offset: 0; size: 2; index to first row
        $fr = self::getUInt2d($subData, 0) + 1;

        // offset: 2; size: 2; index to last row
        $lr = self::getUInt2d($subData, 2) + 1;

        // offset: 4; size: 2; index to first column
        $fc = self::getUInt2d($subData, 4);

        // offset: 6; size: 2; index to last column
        $lc = self::getUInt2d($subData, 6);

        // check values
        if ($fr > $lr || $fc > $lc) {
            throw new ReaderException('Not a cell range address');
        }

        // column index to letter
        $fc = Coordinate::stringFromColumnIndex($fc + 1);
        $lc = Coordinate::stringFromColumnIndex($lc + 1);

        if ($fr == $lr && $fc == $lc) {
            return "$fc$fr";
        }

        return "$fc$fr:$lc$lr";
    }

    /**
     * Reads a cell range address in BIFF8 e.g. 'A2:B6' or '$A$2:$B$6'
     * there are flags indicating whether column/row index is relative
     * section 3.3.4.
     */
    protected static function readBIFF8CellRangeAddress(string $subData): string
    {
        // todo: if cell range is just a single cell, should this funciton
        // not just return e.g. 'A1' and not 'A1:A1' ?

        // offset: 0; size: 2; index to first row (0... 65535) (or offset (-32768... 32767))
        $fr = self::getUInt2d($subData, 0) + 1;

        // offset: 2; size: 2; index to last row (0... 65535) (or offset (-32768... 32767))
        $lr = self::getUInt2d($subData, 2) + 1;

        // offset: 4; size: 2; index to first column or column offset + relative flags

        // bit: 7-0; mask 0x00FF; column index
        $fc = Coordinate::stringFromColumnIndex((0x00FF & self::getUInt2d($subData, 4)) + 1);

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getUInt2d($subData, 4))) {
            $fc = '$' . $fc;
        }

        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getUInt2d($subData, 4))) {
            $fr = '$' . $fr;
        }

        // offset: 6; size: 2; index to last column or column offset + relative flags

        // bit: 7-0; mask 0x00FF; column index
        $lc = Coordinate::stringFromColumnIndex((0x00FF & self::getUInt2d($subData, 6)) + 1);

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getUInt2d($subData, 6))) {
            $lc = '$' . $lc;
        }

        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getUInt2d($subData, 6))) {
            $lr = '$' . $lr;
        }

        return "$fc$fr:$lc$lr";
    }

    /**
     * Reads a cell range address in BIFF8 for shared formulas. Uses positive and negative values for row and column
     * to indicate offsets from a base cell
     * section 3.3.4.
     *
     * @param string $baseCell Base cell
     *
     * @return string Cell range address
     */
    protected static function readBIFF8CellRangeAddressB(string $subData, string $baseCell = 'A1'): string
    {
        [$baseCol, $baseRow] = Coordinate::indexesFromString($baseCell);
        $baseCol = $baseCol - 1;

        // TODO: if cell range is just a single cell, should this funciton
        // not just return e.g. 'A1' and not 'A1:A1' ?

        // offset: 0; size: 2; first row
        $frIndex = self::getUInt2d($subData, 0); // adjust below

        // offset: 2; size: 2; relative index to first row (0... 65535) should be treated as offset (-32768... 32767)
        $lrIndex = self::getUInt2d($subData, 2); // adjust below

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getUInt2d($subData, 4))) {
            // absolute column index
            // offset: 4; size: 2; first column with relative/absolute flags
            // bit: 7-0; mask 0x00FF; column index
            $fcIndex = 0x00FF & self::getUInt2d($subData, 4);
            $fc = Coordinate::stringFromColumnIndex($fcIndex + 1);
            $fc = '$' . $fc;
        } else {
            // column offset
            // offset: 4; size: 2; first column with relative/absolute flags
            // bit: 7-0; mask 0x00FF; column index
            $relativeFcIndex = 0x00FF & self::getInt2d($subData, 4);
            $fcIndex = $baseCol + $relativeFcIndex;
            $fcIndex = ($fcIndex < 256) ? $fcIndex : $fcIndex - 256;
            $fcIndex = ($fcIndex >= 0) ? $fcIndex : $fcIndex + 256;
            $fc = Coordinate::stringFromColumnIndex($fcIndex + 1);
        }

        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getUInt2d($subData, 4))) {
            // absolute row index
            $fr = $frIndex + 1;
            $fr = '$' . $fr;
        } else {
            // row offset
            $frIndex = ($frIndex <= 32767) ? $frIndex : $frIndex - 65536;
            $fr = $baseRow + $frIndex;
        }

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getUInt2d($subData, 6))) {
            // absolute column index
            // offset: 6; size: 2; last column with relative/absolute flags
            // bit: 7-0; mask 0x00FF; column index
            $lcIndex = 0x00FF & self::getUInt2d($subData, 6);
            $lc = Coordinate::stringFromColumnIndex($lcIndex + 1);
            $lc = '$' . $lc;
        } else {
            // column offset
            // offset: 6; size: 2; last column with relative/absolute flags
            // bit: 7-0; mask 0x00FF; column index
            $relativeLcIndex = 0x00FF & self::getInt2d($subData, 6);
            $lcIndex = $baseCol + $relativeLcIndex;
            $lcIndex = ($lcIndex < 256) ? $lcIndex : $lcIndex - 256;
            $lcIndex = ($lcIndex >= 0) ? $lcIndex : $lcIndex + 256;
            $lc = Coordinate::stringFromColumnIndex($lcIndex + 1);
        }

        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getUInt2d($subData, 6))) {
            // absolute row index
            $lr = $lrIndex + 1;
            $lr = '$' . $lr;
        } else {
            // row offset
            $lrIndex = ($lrIndex <= 32767) ? $lrIndex : $lrIndex - 65536;
            $lr = $baseRow + $lrIndex;
        }

        return "$fc$fr:$lc$lr";
    }
}
