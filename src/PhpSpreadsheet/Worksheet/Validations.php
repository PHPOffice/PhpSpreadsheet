<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\CellRange;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;

class Validations
{
    /**
     * Validate a cell address.
     *
     * @param null|array{0: int, 1: int}|CellAddress|string $cellAddress Coordinate of the cell as a string, eg: 'C5';
     *               or as an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     */
    public static function validateCellAddress(null|CellAddress|string|array $cellAddress): string
    {
        if (is_string($cellAddress)) {
            [$worksheet, $address] = Worksheet::extractSheetTitle($cellAddress, true);
//            if (!empty($worksheet) && $worksheet !== $this->getTitle()) {
//                throw new Exception('Reference is not for this worksheet');
//            }

            return empty($worksheet) ? strtoupper("$address") : $worksheet . '!' . strtoupper("$address");
        }

        if (is_array($cellAddress)) {
            $cellAddress = CellAddress::fromColumnRowArray($cellAddress);
        }

        return (string) $cellAddress;
    }

    /**
     * Validate a cell address or cell range.
     *
     * @param AddressRange<CellAddress>|AddressRange<int>|AddressRange<string>|array{0: int, 1: int, 2: int, 3: int}|array{0: int, 1: int}|CellAddress|int|string $cellRange Coordinate of the cells as a string, eg: 'C5:F12';
     *               or as an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 12]),
     *               or as a CellAddress or AddressRange object.
     */
    public static function validateCellOrCellRange(AddressRange|CellAddress|int|string|array $cellRange): string
    {
        if (is_string($cellRange) || is_numeric($cellRange)) {
            // Convert a single column reference like 'A' to 'A:A',
            //    a single row reference like '1' to '1:1'
            $cellRange = (string) preg_replace('/^([A-Z]+|\d+)$/', '${1}:${1}', (string) $cellRange);
        } elseif (is_object($cellRange) && $cellRange instanceof CellAddress) {
            $cellRange = new CellRange($cellRange, $cellRange);
        }

        return self::validateCellRange($cellRange);
    }

    private const SETMAXROW = '${1}1:${2}' . AddressRange::MAX_ROW;
    private const SETMAXCOL = 'A${1}:' . AddressRange::MAX_COLUMN . '${2}';

    /**
     * Validate a cell range.
     *
     * @param AddressRange<CellAddress>|AddressRange<int>|AddressRange<string>|array{0: int, 1: int, 2: int, 3: int}|array{0: int, 1: int}|string $cellRange Coordinate of the cells as a string, eg: 'C5:F12';
     *               or as an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 12]),
     *               or as an AddressRange object.
     */
    public static function validateCellRange(AddressRange|string|array $cellRange): string
    {
        if (is_string($cellRange)) {
            [$worksheet, $addressRange] = Worksheet::extractSheetTitle($cellRange, true);

            // Convert Column ranges like 'A:C' to 'A1:C1048576'
            //      or Row ranges like '1:3' to 'A1:XFD3'
            $addressRange = (string) preg_replace(
                ['/^([A-Z]+):([A-Z]+)$/i', '/^(\\d+):(\\d+)$/'],
                [self::SETMAXROW, self::SETMAXCOL],
                $addressRange ?? ''
            );

            return empty($worksheet) ? strtoupper($addressRange) : $worksheet . '!' . strtoupper($addressRange);
        }

        if (is_array($cellRange)) {
            switch (count($cellRange)) {
                case 4:
                    $from = [$cellRange[0], $cellRange[1]];
                    $to = [$cellRange[2], $cellRange[3]];

                    break;
                case 2:
                    $from = [$cellRange[0], $cellRange[1]];
                    $to = [$cellRange[0], $cellRange[1]];

                    break;
                default:
                    throw new SpreadsheetException('CellRange array length must be 2 or 4');
            }
            $cellRange = new CellRange(CellAddress::fromColumnRowArray($from), CellAddress::fromColumnRowArray($to));
        }

        return (string) $cellRange;
    }

    public static function definedNameToCoordinate(string $coordinate, Worksheet $worksheet): string
    {
        // Uppercase coordinate
        $coordinate = strtoupper($coordinate);
        // Eliminate leading equal sign
        $testCoordinate = (string) preg_replace('/^=/', '', $coordinate);
        $defined = $worksheet->getParentOrThrow()->getDefinedName($testCoordinate, $worksheet);
        if ($defined !== null) {
            if ($defined->getWorksheet() === $worksheet && !$defined->isFormula()) {
                $coordinate = (string) preg_replace('/^=/', '', $defined->getValue());
            }
        }

        return $coordinate;
    }
}
