<?php

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FreezePaneTest extends AbstractFunctional
{
    public static function providerFormats(): array
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
    public function testFreezePane($format): void
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

    /**
     * @dataProvider providerFormats
     *
     * @param string $format
     */
    public function testFreezePaneWithInvalidSelectedCells($format): void
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
        self::assertSame('F5', $reloadedActive->getSelectedCells());
    }

    /**
     * @dataProvider providerFormats
     *
     * @param string $format
     */
    public function testFreezePaneUserSelectedCell($format): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('A1', 'Header1');
        $worksheet->setCellValue('B1', 'Header2');
        $worksheet->setCellValue('C1', 'Header3');
        $worksheet->setCellValue('A2', 'Data1');
        $worksheet->setCellValue('B2', 'Data2');
        $worksheet->setCellValue('C2', 'Data3');
        $worksheet->setCellValue('A3', 'Data4');
        $worksheet->setCellValue('B3', 'Data5');
        $worksheet->setCellValue('C3', 'Data6');
        $worksheet->freezePane('A2');
        $worksheet->setSelectedCells('C3');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();

        $expected = 'C3';
        self::assertSame($expected, $reloadedActive->getSelectedCells());
    }

    /**
     * @dataProvider providerFormats
     *
     * @param string $format
     */
    public function testNoFreezePaneUserSelectedCell($format): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setCellValue('A1', 'Header1');
        $worksheet->setCellValue('B1', 'Header2');
        $worksheet->setCellValue('C1', 'Header3');
        $worksheet->setCellValue('A2', 'Data1');
        $worksheet->setCellValue('B2', 'Data2');
        $worksheet->setCellValue('C2', 'Data3');
        $worksheet->setCellValue('A3', 'Data4');
        $worksheet->setCellValue('B3', 'Data5');
        $worksheet->setCellValue('C3', 'Data6');
        //$worksheet->freezePane('A2');
        $worksheet->setSelectedCells('C3');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();

        $expected = 'C3';
        self::assertSame($expected, $reloadedActive->getSelectedCells());
    }
}
