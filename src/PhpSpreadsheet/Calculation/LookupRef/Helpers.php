<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Helpers
{
    public const CELLADDRESS_USE_A1 = true;

    public const CELLADDRESS_USE_R1C1 = false;

    private static function convertR1C1(string &$cellAddress1, ?string &$cellAddress2, bool $a1, ?int $baseRow = null, ?int $baseCol = null): string
    {
        if ($a1 === self::CELLADDRESS_USE_R1C1) {
            $cellAddress1 = AddressHelper::convertToA1($cellAddress1, $baseRow ?? 1, $baseCol ?? 1);
            if ($cellAddress2) {
                $cellAddress2 = AddressHelper::convertToA1($cellAddress2, $baseRow ?? 1, $baseCol ?? 1);
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

    public static function extractCellAddresses(string $cellAddress, bool $a1, Worksheet $sheet, string $sheetName = '', ?int $baseRow = null, ?int $baseCol = null): array
    {
        $cellAddress1 = $cellAddress;
        $cellAddress2 = null;
        $namedRange = DefinedName::resolveName($cellAddress1, $sheet, $sheetName);
        if ($namedRange !== null) {
            $workSheet = $namedRange->getWorkSheet();
            $sheetTitle = ($workSheet === null) ? '' : $workSheet->getTitle();
            $value = (string) preg_replace('/^=/', '', $namedRange->getValue());
            self::adjustSheetTitle($sheetTitle, $value);
            $cellAddress1 = $sheetTitle . $value;
            $cellAddress = $cellAddress1;
            $a1 = self::CELLADDRESS_USE_A1;
        }
        if (str_contains($cellAddress, ':')) {
            [$cellAddress1, $cellAddress2] = explode(':', $cellAddress);
        }
        $cellAddress = self::convertR1C1($cellAddress1, $cellAddress2, $a1, $baseRow, $baseCol);

        return [$cellAddress1, $cellAddress2, $cellAddress];
    }

    public static function extractWorksheet(string $cellAddress, Cell $cell): array
    {
        $sheetName = '';
        if (str_contains($cellAddress, '!')) {
            [$sheetName, $cellAddress] = Worksheet::extractSheetTitle($cellAddress, true);
            $sheetName = trim($sheetName, "'");
        }

        $worksheet = ($sheetName !== '')
            ? $cell->getWorksheet()->getParentOrThrow()->getSheetByName($sheetName)
            : $cell->getWorksheet();

        return [$cellAddress, $worksheet, $sheetName];
    }
}
