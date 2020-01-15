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

    public function providerFormatsAndActiveFlag()
    {
        return [
            ['Xls', true],
            ['Xls', null],
            ['Xls', false],
            ['Xlsx', true],
            ['Xlsx', null],
            ['Xlsx', false],
        ];
    }

    /**
     * @dataProvider providerFormatsAndActiveFlag
     *
     * @param string $format
     * @param mixed $actv
     */
    public function testFreezePaneWithInvalidSelectedCells($format, $actv)
    {
        $callback = $this->setCallback($format, $actv);
        $cellSplit = 'A7';
        $topLeftCell = 'A24';

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->freezePane('A7', 'A24');
        $worksheet->setSelectedCells('F5');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format, null, $callback);

        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();
        $actualCellSplit = $reloadedActive->getFreezePane();
        $actualTopLeftCell = $reloadedActive->getTopLeftCell();

        self::assertSame($cellSplit, $actualCellSplit, 'should be able to set freeze pane');
        self::assertSame($topLeftCell, $actualTopLeftCell, 'should be able to set the top left cell');
        $expected = ($format === 'Xls' || $actv === true) ? 'F5' : 'A24';
        self::assertSame($expected, $reloadedActive->getSelectedCells());
    }

    public function setActiveTrue($writer)
    {
        $writer->setActiveCellAnywhere(true);
    }

    public function setActiveFalse($writer)
    {
        $writer->setActiveCellAnywhere(false);
    }

    public function setCallback($format, $actv)
    {
        if ($format === 'Xls') {
            return null;
        }
        if ($actv === true) {
            return [$this, 'setActiveTrue'];
        }
        if ($actv === false) {
            return [$this, 'setActiveFalse'];
        }

        return null;
    }

    /**
     * @dataProvider providerFormatsAndActiveFlag
     *
     * @param string $format
     * @param mixed $actv
     */
    public function testFreezePaneUserSelectedCell($format, $actv)
    {
        $callback = $this->setCallback($format, $actv);
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

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format, null, $callback);
        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();

        $expected = ($format === 'Xls' || $actv === true) ? 'C3' : 'A2';
        self::assertSame($expected, $reloadedActive->getSelectedCells());
    }

    /**
     * @dataProvider providerFormatsAndActiveFlag
     *
     * @param string $format
     * @param mixed $actv
     */
    public function testNoFreezePaneUserSelectedCell($format, $actv)
    {
        $callback = $this->setCallback($format, $actv);
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

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format, null, $callback);
        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();

        //$expected = ($format === 'Xls' || $actv === true) ? 'C3' : 'A2';
        $expected = 'C3';
        self::assertSame($expected, $reloadedActive->getSelectedCells());
    }
}
