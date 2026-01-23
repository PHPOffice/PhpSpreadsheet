<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ToArrayOldCalculatedValueTest extends AbstractFunctional
{
    public function testOldCalculatedValue(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([['A', 'B', 'C', '=1+2']]);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();
        $rsheet = $reloadedSpreadsheet->getActiveSheet();
        $rsheet->setCellValue('D1', '=1+3');
        $array1 = $rsheet->toArray(formatData: false, calculateFormulas: false, oldCalculatedValue: true);
        self::assertSame([['A', 'B', 'C', 3]], $array1, 'uses value as read from spreadsheet');
        $array2 = $rsheet->toArray(formatData: false);
        self::assertSame([['A', 'B', 'C', 4]], $array2, 'uses newly calculated value');

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
