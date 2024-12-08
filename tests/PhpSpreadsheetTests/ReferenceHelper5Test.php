<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ReferenceHelper5Test extends TestCase
{
    public function testIssue4246(): void
    {
        // code below would have thrown exception because
        // row and column were swapped in code
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 987654;
        $rowMinus1 = $row - 1;
        $rowPlus1 = $row + 1;
        $sheet->getCell("A$rowMinus1")->setValue(1);
        $sheet->getCell("B$rowMinus1")->setValue(2);
        $sheet->getCell("C$rowMinus1")->setValue(3);
        $sheet->getStyle("A$rowMinus1")->getFont()->setBold(true);
        $sheet->getCell("A$row")->setValue(1);
        $sheet->getCell("B$row")->setValue(2);
        $sheet->getCell("C$row")->setValue(3);
        $sheet->getStyle("B$row")->getFont()->setBold(true);

        $sheet->insertNewRowBefore($row);
        self::assertTrue(
            $sheet->getStyle("A$row")->getFont()->getBold()
        );
        self::assertFalse(
            $sheet->getStyle("B$row")->getFont()->getBold()
        );
        self::assertFalse(
            $sheet->getStyle("A$rowPlus1")->getFont()->getBold()
        );
        self::assertTrue(
            $sheet->getStyle("B$rowPlus1")->getFont()->getBold()
        );
        $spreadsheet->disconnectWorksheets();
    }
}
