<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Issue5032Test extends TestCase
{
    public function testMissingSheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '=MAX(Personnel_Salaries!C:E)');
        $sheet->setCellValue('B1', '=IFERROR(MAX(sheet2!5:7),"oops")');
        self::assertSame('#REF!', $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame('oops', $sheet->getCell('B1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testBoundedRangeOnMissingSheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '=MAX(Personnel_Salaries!C1:C5)');
        $sheet->setCellValue('B1', '=IFERROR(MAX(sheet2!X3:Z4),"oops")');
        self::assertSame('#REF!', $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame('oops', $sheet->getCell('B1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
