<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Issue4791Test extends TestCase
{
    public function testCyclicRangeWithExternalReference(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', '=A1:A2');
        $sheet->setCellValue('A2', '=[1]Test!A2');
        $sheet->getCell('A2')->setCalculatedValue('#REF!');
        self::assertSame(0, $sheet->getCell('A1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
