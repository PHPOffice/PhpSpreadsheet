<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Csv;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class CsvArrayTest extends AbstractFunctional
{
    public function testArray(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue(1);
        $sheet->getCell('A2')->setValue(1);
        $sheet->getCell('A3')->setValue(3);
        $sheet->getCell('B1')->setValue('=UNIQUE(A1:A3)');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Csv');
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        self::assertEquals('1', $sheet->getCell('A1')->getValue());
        self::assertEquals('1', $sheet->getCell('A2')->getValue());
        self::assertEquals('3', $sheet->getCell('A3')->getValue());
        self::assertEquals('1', $sheet->getCell('B1')->getValue());
        self::assertEquals('3', $sheet->getCell('B2')->getValue());
        self::assertNull($sheet->getCell('B3')->getValue());
        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testInlineArrays(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=UNIQUE({1;1;2;1;3;2;4;4;4})');
        $sheet->getCell('D1')->setValue('=UNIQUE({1,1,2,1,3,2,4,4,4},true)');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Csv');
        $sheet = $reloadedSpreadsheet->getActiveSheet();
        $expected = [
            ['1', null, null, '1', '2', '3', '4'],
            ['2', null, null, null, null, null, null],
            ['3', null, null, null, null, null, null],
            ['4', null, null, null, null, null, null],
        ];
        self::assertSame($expected, $sheet->toArray());
        $spreadsheet->disconnectWorksheets();
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
