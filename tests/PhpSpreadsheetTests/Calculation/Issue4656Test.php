<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class Issue4656Test extends TestCase
{
    public function testIssue4656(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 2);
        $sheet->setCellValue('C1', 1);
        $sheet->setCellValue('D1', 3);
        $sheet->setCellValue('E1', 2);
        $sheet->setCellValue('A1', '=RANK(B1,(C1,E1))');
        $sheet->setCellValue('A2', '=RANK(B1,C1:E1)');
        self::assertSame(1, $sheet->getCell('A1')->getCalculatedValue());
        self::assertSame(2, $sheet->getCell('A2')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testIssue4656Original(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 1);
        $sheet->setCellValue('C1', 1);
        $sheet->setCellValue('D1', 2);
        $sheet->setCellValue('A1', '=RANK(B1,(C1,D1))');
        self::assertSame(2, $sheet->getCell('A1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public static function testCellRanges(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [10, 100, 1000],
            [20, 200, 2000],
            [30, 300, 3000],
            [40, 400, 4000],
            [50, 500, 5000],
        ]);
        $sheet->setCellValue('F1', '=SUM((A1:A3, A5), (B1:B2, B4:B5), C3)');
        self::assertSame(4310, $sheet->getCell('F1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
