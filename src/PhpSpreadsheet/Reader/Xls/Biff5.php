<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class Biff5 extends Xls
{
    /**
     * Reads a cell range address in BIFF5 e.g. 'A2:B6' or 'A1'
     * always fixed range
     * section 2.5.14.
     */
    public static function readBIFF5CellRangeAddressFixed(string $subData): string
    {
        // offset: 0; size: 2; index to first row
        $fr = self::getUInt2d($subData, 0) + 1;

        // offset: 2; size: 2; index to last row
        $lr = self::getUInt2d($subData, 2) + 1;

        // offset: 4; size: 1; index to first column
        $fc = ord($subData[4]);

        // offset: 5; size: 1; index to last column
        $lc = ord($subData[5]);

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
     * Read BIFF5 cell range address list
     * section 2.5.15.
     *
     * @return array{size: int, cellRangeAddresses: string[]}
     */
    public static function readBIFF5CellRangeAddressList(string $subData): array
    {
        $cellRangeAddresses = [];

        // offset: 0; size: 2; number of the following cell range addresses
        $nm = self::getUInt2d($subData, 0);

        $offset = 2;
        // offset: 2; size: 6 * $nm; list of $nm (fixed) cell range addresses
        for ($i = 0; $i < $nm; ++$i) {
            $cellRangeAddresses[] = self::readBIFF5CellRangeAddressFixed(substr($subData, $offset, 6));
            $offset += 6;
        }

        return [
            'size' => 2 + 6 * $nm,
            'cellRangeAddresses' => $cellRangeAddresses,
        ];
    }
}
