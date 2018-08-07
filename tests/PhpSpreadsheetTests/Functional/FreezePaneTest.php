<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FreezePaneTest extends AbstractFunctional
{
    public function providerFormats()
    {
        return [
            ['Xls'],
            ['Xlsx'],
        ];
    }

    /**
     * @dataProvider providerFormats
     *
     * @param string $format
     */
    public function testFreezePane($format)
    {
        $cellSplit = 'B4';
        $topLeftCell = 'E7';

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->freezePane($cellSplit, $topLeftCell);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();
        $actualCellSplit = $reloadedActive->getFreezePane();
        $actualTopLeftCell = $reloadedActive->getTopLeftCell();

        self::assertSame($cellSplit, $actualCellSplit, 'should be able to set freeze pane');
        self::assertSame($topLeftCell, $actualTopLeftCell, 'should be able to set the top left cell');
    }

    public function providerFormatsInvalidSelectedCells()
    {
        return [
            ['Xlsx'],
        ];
    }

    /**
     * @dataProvider providerFormatsInvalidSelectedCells
     *
     * @param string $format
     */
    public function testFreezePaneWithInvalidSelectedCells($format)
    {
        $cellSplit = 'A7';
        $topLeftCell = 'A24';

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->freezePane('A7', 'A24');
        $worksheet->setSelectedCells('F5');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);

        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();
        $actualCellSplit = $reloadedActive->getFreezePane();
        $actualTopLeftCell = $reloadedActive->getTopLeftCell();

        self::assertSame($cellSplit, $actualCellSplit, 'should be able to set freeze pane');
        self::assertSame($topLeftCell, $actualTopLeftCell, 'should be able to set the top left cell');
        self::assertSame('A24', $reloadedActive->getSelectedCells(), 'selected cell should default to be first cell after the freeze pane');
    }
}
