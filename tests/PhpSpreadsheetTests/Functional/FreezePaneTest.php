<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Functional;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Pane;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

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
     */
    public function testFreezePane(string $format): void
    {
        $cellSplit = 'B4';
        $topLeftCell = 'E7';

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->freezePane($cellSplit, $topLeftCell);

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $spreadsheet->disconnectWorksheets();

        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();
        $actualCellSplit = $reloadedActive->getFreezePane();
        $actualTopLeftCell = $reloadedActive->getTopLeftCell();

        self::assertSame($cellSplit, $actualCellSplit, 'should be able to set freeze pane');
        self::assertSame($topLeftCell, $actualTopLeftCell, 'should be able to set the top left cell');
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * @dataProvider providerFormats
     */
    public function testFreezePaneWithInvalidSelectedCells(string $format): void
    {
        $cellSplit = 'A7';
        $topLeftCell = 'A24';

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $worksheet->freezePane('A7', 'A24');
        $worksheet->setSelectedCells('F5');

        $reloadedSpreadsheet = $this->writeAndReload($spreadsheet, $format);
        $spreadsheet->disconnectWorksheets();

        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();
        $actualCellSplit = $reloadedActive->getFreezePane();
        $actualTopLeftCell = $reloadedActive->getTopLeftCell();

        self::assertSame($cellSplit, $actualCellSplit, 'should be able to set freeze pane');
        self::assertSame($topLeftCell, $actualTopLeftCell, 'should be able to set the top left cell');
        self::assertSame('F5', $reloadedActive->getSelectedCells());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * @dataProvider providerFormats
     */
    public function testFreezePaneUserSelectedCell(string $format): void
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
        $spreadsheet->disconnectWorksheets();
        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();

        self::assertSame('C3', $reloadedActive->getSelectedCells());
        self::assertSame('A2', $reloadedActive->getFreezePane());
        if ($format === 'Xlsx') {
            $writer = new XlsxWriter($reloadedSpreadsheet);
            $writerSheet = new XlsxWriter\Worksheet($writer);
            $data = $writerSheet->writeWorksheet($reloadedActive);
            $expectedString = '<pane ySplit="1" activePane="bottomLeft" state="frozen" topLeftCell="A2"/><selection pane="bottomLeft" activeCell="C3" sqref="C3"/>';
            self::assertStringContainsString($expectedString, $data);

            $reloadedActive->freezePane('C1');
            $reloadedActive->setSelectedCells('A2');
            $writer = new XlsxWriter($reloadedSpreadsheet);
            $writerSheet = new XlsxWriter\Worksheet($writer);
            $data = $writerSheet->writeWorksheet($reloadedActive);
            $expectedString = '<pane xSplit="2" activePane="topRight" state="frozen" topLeftCell="C1"/><selection pane="topRight" activeCell="A2" sqref="A2"/>';
            self::assertStringContainsString($expectedString, $data);
        }
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    /**
     * @dataProvider providerFormats
     */
    public function testNoFreezePaneUserSelectedCell(string $format): void
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
        $spreadsheet->disconnectWorksheets();
        // Read written file
        $reloadedActive = $reloadedSpreadsheet->getActiveSheet();

        $expected = 'C3';
        self::assertSame($expected, $reloadedActive->getSelectedCells());
        $reloadedSpreadsheet->disconnectWorksheets();
    }

    public function testFreezePaneWithSelectedCells(): void
    {
        $spreadsheet = new Spreadsheet();
        $cellSplit = 'C4';
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->freezePane($cellSplit);
        $sheet->setSelectedCells('A2');
        self::assertSame('topLeft', $sheet->getActivePane());
        $sheet->setSelectedCells('D3');
        self::assertSame('topRight', $sheet->getActivePane());
        $sheet->setSelectedCells('B5');
        self::assertSame('bottomLeft', $sheet->getActivePane());
        $sheet->setSelectedCells('F7');
        self::assertSame('bottomRight', $sheet->getActivePane());
        $expected = [
            'topLeft' => new Pane('topLeft', 'A2', 'A2'),
            'topRight' => new Pane('topRight', 'D3', 'D3'),
            'bottomLeft' => new Pane('bottomLeft', 'B5', 'B5'),
            'bottomRight' => new Pane('bottomRight', 'F7', 'F7'),
        ];
        self::assertEquals($expected, $sheet->getPanes());
        self::assertSame('F7', $sheet->getSelectedCells());

        $sheet = $spreadsheet->createSheet();
        $cellSplit = 'A2';
        $sheet->freezePane($cellSplit);
        $sheet->setSelectedCells('B1');
        self::assertSame('topLeft', $sheet->getActivePane());
        $sheet->setSelectedCells('C7');
        self::assertSame('bottomLeft', $sheet->getActivePane());
        $expected = [
            'topLeft' => new Pane('topLeft', 'B1', 'B1'),
            'topRight' => null,
            'bottomLeft' => new Pane('bottomLeft', 'C7', 'C7'),
            'bottomRight' => null,
        ];
        self::assertEquals($expected, $sheet->getPanes());
        self::assertSame('C7', $sheet->getSelectedCells());

        $sheet = $spreadsheet->createSheet();
        $cellSplit = 'D1';
        $sheet->freezePane($cellSplit);
        $sheet->setSelectedCells('B1');
        self::assertSame('topLeft', $sheet->getActivePane());
        $sheet->setSelectedCells('G3');
        self::assertSame('topRight', $sheet->getActivePane());
        $expected = [
            'topRight' => new Pane('topRight', 'G3', 'G3'),
            'bottomRight' => null,
            'topLeft' => new Pane('topLeft', 'B1', 'B1'),
            'bottomLeft' => null,
        ];
        self::assertEquals($expected, $sheet->getPanes());
        self::assertSame('G3', $sheet->getSelectedCells());

        $sheet = $spreadsheet->createSheet();
        $sheet->setSelectedCells('D7');
        self::assertEmpty($sheet->getActivePane());
        $expected = [
            'topRight' => null,
            'bottomRight' => null,
            'topLeft' => null,
            'bottomLeft' => null,
        ];
        self::assertEquals($expected, $sheet->getPanes());
        self::assertSame('D7', $sheet->getSelectedCells());

        $spreadsheet->disconnectWorksheets();
    }

    public function testFreezePaneViaPaneState(): void
    {
        $spreadsheet = new Spreadsheet();
        $cellSplit = ['C4', 2, 3];
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setXSplit($cellSplit[1])
            ->setYSplit($cellSplit[2])
            ->setPaneState(Worksheet::PANE_SPLIT);
        self::assertNull($sheet->getFreezePane());

        $sheet = $spreadsheet->createSheet();
        $sheet->setXSplit($cellSplit[1])
            ->setYSplit($cellSplit[2])
            ->setPaneState(Worksheet::PANE_FROZEN);
        self::assertSame($cellSplit[0], $sheet->getFreezePane());
        $sheet->setSelectedCells('A2');
        self::assertSame('topLeft', $sheet->getActivePane());
        $sheet->setSelectedCells('D3');
        self::assertSame('topRight', $sheet->getActivePane());
        $sheet->setSelectedCells('B5');
        self::assertSame('bottomLeft', $sheet->getActivePane());
        $sheet->setSelectedCells('F7');
        self::assertSame('bottomRight', $sheet->getActivePane());
        $expected = [
            'topLeft' => new Pane('topLeft', 'A2', 'A2'),
            'topRight' => new Pane('topRight', 'D3', 'D3'),
            'bottomLeft' => new Pane('bottomLeft', 'B5', 'B5'),
            'bottomRight' => new Pane('bottomRight', 'F7', 'F7'),
        ];
        self::assertEquals($expected, $sheet->getPanes());
        self::assertSame('F7', $sheet->getSelectedCells());

        $cellSplit = ['B4', 1, 3];
        $sheet->setXSplit($cellSplit[1]);
        self::assertSame($cellSplit[0], $sheet->getFreezePane());
        self::assertSame('F7', $sheet->getSelectedCells());
        $expected = [
            'topLeft' => null,
            'topRight' => null,
            'bottomLeft' => null,
            'bottomRight' => new Pane('bottomRight', 'F7', 'F7'),
        ];
        self::assertEquals($expected, $sheet->getPanes());

        $cellSplit = ['B8', 1, 7];
        $sheet->setYSplit($cellSplit[2]);
        self::assertSame($cellSplit[0], $sheet->getFreezePane());
        self::assertSame('F7', $sheet->getSelectedCells());
        $expected = [
            'topLeft' => null,
            'bottomRight' => null,
            'bottomLeft' => null,
            'topRight' => new Pane('topRight', 'F7', 'F7'),
        ];
        self::assertEquals($expected, $sheet->getPanes());

        $spreadsheet->disconnectWorksheets();
    }

    public function testAutoWidthDoesNotCorruptActivePane(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(
            [
                [1, 2, 3, 4],
                [5, 6, 7, 8],
                [9, 10, 11, 12],
            ]
        );
        $sheet->freezePane('A2');
        $sheet->setSelectedCell('B1');
        $paneBefore = $sheet->getActivePane();
        self::assertSame('topLeft', $paneBefore);
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->calculateColumnWidths();
        $paneAfter = $sheet->getActivePane();
        self::assertSame('topLeft', $paneAfter);
        $spreadsheet->disconnectWorksheets();
    }
}
