<?php

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class ArrayFormulaTest extends AbstractFunctional
{
    public function testArrayFormulaReadWrite(): void
    {
        // Problem deleting sheet containing local defined name.
        $reader = new Reader();
        $spreadsheet = $reader->load('tests/data/Writer/XLSX/ArrayFormula.xlsx');

        $cellFormulaAttributes = $spreadsheet->getActiveSheet()->getCell('A1')->getFormulaAttributes();

        self::assertArrayHasKey('t', $cellFormulaAttributes);
        self::assertArrayHasKey('ref', $cellFormulaAttributes);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, 'Xlsx');
        $spreadsheet->disconnectWorksheets();

        $reloadedCellFormulaAttributes = $reloadedSpreadsheet->getActiveSheet()->getCell('A1')->getFormulaAttributes();

        self::assertArrayHasKey('t', $reloadedCellFormulaAttributes);
        self::assertArrayHasKey('ref', $reloadedCellFormulaAttributes);

        self::assertSame($cellFormulaAttributes['t'], $reloadedCellFormulaAttributes['t']);
        self::assertSame($cellFormulaAttributes['ref'], $reloadedCellFormulaAttributes['ref']);

        $reloadedSpreadsheet->disconnectWorksheets();
    }
}
