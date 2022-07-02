<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Ods;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ArrayFormulaTest extends AbstractFunctional
{
    public function testArrayFormulaWriter(): void
    {
        $arrayFormulaRange = 'B2:C3';
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('B2', '={2,3}*{4;5}', true, $arrayFormulaRange);

        $reloaded = $this->writeAndReload($spreadsheet, 'Ods');

        $cell = $reloaded->getActiveSheet()->getCell('B2');
        self::assertTrue($cell->isArrayFormula());
        self::assertSame($arrayFormulaRange, $cell->arrayFormulaRange());
    }
}
