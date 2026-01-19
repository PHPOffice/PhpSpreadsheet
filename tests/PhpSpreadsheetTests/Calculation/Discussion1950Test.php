<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Discussion1950Test extends TestCase
{
    public function testMultipleUnions(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1, 2],
            [3, 4],
        ]);
        $sheet->setCellValue('A5', '=SUM((A1,A2),(B1,B2))');
        self::assertSame(10, $sheet->getCell('A5')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testUnexpectedUnion(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1],
            [-1],
            [2],
            [1],
            ['=RANK(A1,(A2,A3,A4))'],
        ]);
        self::assertSame(2, $sheet->getCell('A5')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
