<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\LookupRef;

use PhpOffice\PhpSpreadsheet\Cell\AddressHelper;
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

    public static function extractCellAddresses(string $cellAddress, bool $a1, Spreadsheet $spreadsheet, Worksheet $sheet): array
    {
        $cellAddress1 = $cellAddress;
        $cellAddress2 = null;
        $namedRanges = $spreadsheet->getNamedRanges();
        foreach ($namedRanges as $namedRange) {
            $scope = $namedRange->getScope();
            if ($cellAddress1 === $namedRange->getName() && ($scope === null || $scope === $sheet)) {
                $sheet = $namedRange->getWorkSheet()->getTitle() . '!';
                $cellAddress1 = $sheet . $namedRange->getValue();
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
}
