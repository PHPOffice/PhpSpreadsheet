<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\CellRange;

class Validations
{
    /**
     * Validate a cell address.
     *
     * @param null|array<int>|CellAddress|string $cellAddress Coordinate of the cell as a string, eg: 'C5';
     *               or as an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     */
    public static function validateCellAddress($cellAddress): string
    {
        if (is_string($cellAddress)) {
            [$worksheet, $address] = Worksheet::extractSheetTitle($cellAddress, true);
//            if (!empty($worksheet) && $worksheet !== $this->getTitle()) {
//                throw new Exception('Reference is not for this worksheet');
//            }

            return empty($worksheet) ? strtoupper($address) : $worksheet . '!' . strtoupper($address);
        }

        if (is_array($cellAddress)) {
            $cellAddress = CellAddress::fromColumnRowArray($cellAddress);
        }

        return (string) $cellAddress;
    }

    /**
     * Validate a cell address or cell range.
     *
     * @param AddressRange|array<int>|CellAddress|int|string $cellRange Coordinate of the cells as a string, eg: 'C5:F12';
     *               or as an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 12]),
     *               or as a CellAddress or AddressRange object.
     */
    public static function validateCellOrCellRange($cellRange): string
    {
        if (is_string($cellRange) || is_numeric($cellRange)) {
            $cellRange = (string) $cellRange;
            // Convert a single column reference like 'A' to 'A:A'
            $cellRange = (string) preg_replace('/^([A-Z]+)$/', '${1}:${1}', $cellRange);
            // Convert a single row reference like '1' to '1:1'
            $cellRange = (string) preg_replace('/^(\d+)$/', '${1}:${1}', $cellRange);
        } elseif (is_object($cellRange) && $cellRange instanceof CellAddress) {
            $cellRange = new CellRange($cellRange, $cellRange);
        }

        return self::validateCellRange($cellRange);
    }

    /**
     * Validate a cell range.
     *
     * @param AddressRange|array<int>|string $cellRange Coordinate of the cells as a string, eg: 'C5:F12';
     *               or as an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 12]),
     *               or as an AddressRange object.
     */
    public static function validateCellRange($cellRange): string
    {
        if (is_string($cellRange)) {
            [$worksheet, $addressRange] = Worksheet::extractSheetTitle($cellRange, true);

            // Convert Column ranges like 'A:C' to 'A1:C1048576'
            $addressRange = (string) preg_replace('/^([A-Z]+):([A-Z]+)$/', '${1}1:${2}1048576', $addressRange);
            // Convert Row ranges like '1:3' to 'A1:XFD3'
            $addressRange = (string) preg_replace('/^(\\d+):(\\d+)$/', 'A${1}:XFD${2}', $addressRange);

            return empty($worksheet) ? strtoupper($addressRange) : $worksheet . '!' . strtoupper($addressRange);
        }

        if (is_array($cellRange)) {
            [$from, $to] = array_chunk($cellRange, 2);
            $cellRange = new CellRange(CellAddress::fromColumnRowArray($from), CellAddress::fromColumnRowArray($to));
        }

        return (string) $cellRange;
    }

    public static function definedNameToCoordinate(string $coordinate, Worksheet $worksheet): string
    {
        // Uppercase coordinate
        $testCoordinate = strtoupper($coordinate);
        // Eliminate leading equal sign
        $testCoordinate = (string) preg_replace('/^=/', '', $coordinate);
        $defined = $worksheet->getParent()->getDefinedName($testCoordinate, $worksheet);
        if ($defined !== null) {
            if ($defined->getWorksheet() === $worksheet && !$defined->isFormula()) {
                $coordinate = (string) preg_replace('/^=/', '', $defined->getValue());
            }
        }

        return $coordinate;
    }
}
