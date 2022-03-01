<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class NullEqualsZeroTest extends TestCase
{
    public function testNullEqualsZero(): void
    {
        // Confirm that NULL<>0 returns false
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Item', 'QTY', 'RATE', 'RATE', 'Total'],
            ['Bricks', 1000, 'Each', 0.55, '=IF(B2<>0,B2*D2,"INCL.")'],
            ['Cement', null, null, null, '=IF(B3<>0,B3*D3,"INCL.")'],
            ['Labour', 10, 'Hour', 45.00, '=IF(B4<>0,B4*D4,"INCL.")'],
        ]);
        $sheet->setCellValue('A6', 'Total');
        $sheet->setCellValue('E6', '=SUM(E1:E5)');
        self::assertEquals(550.00, $sheet->getCell('E2')->getCalculatedValue());
        self::assertSame('INCL.', $sheet->getCell('E3')->getCalculatedValue());
        self::assertEquals(450.00, $sheet->getCell('E4')->getCalculatedValue());
        self::assertEquals(1000.00, $sheet->getCell('E6')->getCalculatedValue());

        $sheet->setCellValue('Z1', '=Z2=0');
        self::assertTrue($sheet->getCell('Z1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }
}
