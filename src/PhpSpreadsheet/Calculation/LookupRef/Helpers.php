<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Helpers
{
    private static function convertR1C1(string &$cellAddress1, ?string &$cellAddress2, bool $a1): string
    {
        if (!$a1) {
            $cellAddress1 = AddressHelper::convertToA1($cellAddress1);
            if ($cellAddress2) {
                $cellAddress2 = AddressHelper::convertToA1($cellAddress2);
            }
        }

        return $cellAddress1 . ($cellAddress2 ? ":$cellAddress2" : '');
    }

    private static function adjustSheetTitle(string &$sheetTitle, ?string $value): void
    {
        if ($sheetTitle) {
            $sheetTitle .= '!';
            if (stripos($value ?? '', $sheetTitle) === 0) {
                $sheetTitle = '';
            }
        }
    }

    private static function useThisName(string $cellAddress1, string $definedName, ?Worksheet $scope, Worksheet $sheet, string $sheetTitle, ?string $sheetName): bool
    {
        return strcasecmp($cellAddress1, $definedName) === 0 && ($scope === null || $scope === $sheet || $sheetTitle === $sheetName);
    }

    public static function extractCellAddresses(string $cellAddress, bool $a1, Spreadsheet $spreadsheet, Worksheet $sheet, ?string $sheetName = null): array
    {
        $cellAddress1 = $cellAddress;
        $cellAddress2 = null;
        $namedRanges = $spreadsheet->getNamedRanges();
        foreach ($namedRanges as $namedRange) {
            $scope = $namedRange->getScope();
            $definedName = $namedRange->getName();
            $workSheet = $namedRange->getWorkSheet();
            $sheetTitle = ($workSheet === null) ? '' : $workSheet->getTitle();
            if (self::useThisName($cellAddress1, $definedName, $scope, $sheet, $sheetTitle, $sheetName)) {
                $value = preg_replace('/^=/', '', $namedRange->getValue());
                self::adjustSheetTitle($sheetTitle, $value);
                $cellAddress1 = $sheetTitle . $value;
                $cellAddress = $cellAddress1;
                $a1 = true;

                break;
            }
        }
        if (strpos($cellAddress, ':') !== false) {
            [$cellAddress1, $cellAddress2] = explode(':', $cellAddress);
        }
        $cellAddress = self::convertR1C1($cellAddress1, $cellAddress2, $a1);

        return [$cellAddress1, $cellAddress2, $cellAddress];
    }

    public static function extractWorksheet(string $cellAddress, Cell $pCell): array
    {
        $sheetName = '';
        if (strpos($cellAddress, '!') !== false) {
            [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
            $sheetName = trim($sheetName, "'");
        }

        $pSheet = ($sheetName !== '')
            ? $pCell->getWorksheet()->getParent()->getSheetByName($sheetName)
            : $pCell->getWorksheet();

        return [$cellAddress, $pSheet, $sheetName];
    }
}
