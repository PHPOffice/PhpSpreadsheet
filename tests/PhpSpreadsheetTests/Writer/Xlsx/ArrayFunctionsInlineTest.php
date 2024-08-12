<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ArrayFunctionsInlineTest extends AbstractFunctional
{
    public function testInlineArrays(): void
    {
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue('=UNIQUE({1;1;2;1;3;2;4;4;4})');
        $sheet->getCell('D1')->setValue('=UNIQUE({1,1,2,1,3,2,4,4,4},true)');
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        Calculation::getInstance($reloadedSpreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $expected = [
            ['=UNIQUE({1;1;2;1;3;2;4;4;4})', null, null, '=UNIQUE({1,1,2,1,3,2,4,4,4},true)', 2, 3, 4],
            [2, null, null, null, null, null, null],
            [3, null, null, null, null, null, null],
            [4, null, null, null, null, null, null],
        ];
        self::assertSame($expected, $rsheet->toArray(null, false, false));
        self::assertSame('1', $rsheet->getCell('A1')->getCalculatedValueString());
        self::assertSame('1', $rsheet->getCell('D1')->getCalculatedValueString());
        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
