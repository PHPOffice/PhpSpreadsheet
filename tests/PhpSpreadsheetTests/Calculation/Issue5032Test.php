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
        $sheet->setCellValue('A1', '=MAX(Personnel_Salaries!C:C)');
        $sheet->setCellValue('B1', '=IFERROR(MAX(sheet2!Z:Z),"oops")');
        self::assertSame('#REF!', $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame('oops', $sheet->getCell('B1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
