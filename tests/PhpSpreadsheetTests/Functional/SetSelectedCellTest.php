<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SetSelectedCellTest extends AbstractFunctional
{
    public function providerFormats()
    {
        return [
            ['Xls'],
            ['Xlsx']
        ];
    }

    /**
     * @dataProvider providerFormats
     *
     * @param string $format
     */
    public function testSetSelectedCell($format)
    {
        $cellSplit = 'B4';
        $topLeftCell = 'E7';
        $selectedCell = 'A1';

        // Set selected cell
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->setSelectedCell($selectedCell);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        // Read celected cell from written file
        $newSelectedCell = $reloadedSpreadsheet->getActiveSheet()->getSelectedCells();
        self::assertSame($selectedCell, $newSelectedCell, 'should be able to set selected cell');

        // Set selected cell with using FreezePane
        $spreadsheet = new Spreadsheet();
        $activeSheet = $spreadsheet->getActiveSheet();
        $activeSheet->freezePane($cellSplit, $topLeftCell);
        $activeSheet->setSelectedCell($selectedCell);
        $spreadsheet->getActiveSheet()->setSelectedCell($selectedCell);
        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        // Read celected cell from written file
        $newSelectedCell = $reloadedSpreadsheet->getActiveSheet()->getSelectedCells();
        self::assertSame($selectedCell, $newSelectedCell, 'should be able to set selected cell when using FreezePane');
    }
}
