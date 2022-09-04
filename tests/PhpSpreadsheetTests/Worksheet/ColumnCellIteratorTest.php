<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Exception as Except;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ColumnCellIteratorTest extends TestCase
{
    private const CELL_VALUES =
        [
            [110, 210, 310, 410, 510, 610, 710],
            [120, 220, 320, 420, 520, 620],
            [130, 230, 330, 430, 530, 630],
            [140, 240, 340, 440, 540, 640],
            [150, 250, 350, 450, 550, 650],
            [160, null, 360, null, 560],
        ];

    private static function getPopulatedSheet(Spreadsheet $spreadsheet): Worksheet
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(self::CELL_VALUES);

        return $sheet;
    }

    public function testIteratorFullRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnCellIterator($sheet, 'A');
        $ColumnCellIndexResult = 1;
        self::assertEquals($ColumnCellIndexResult, $iterator->key());

        $values = [];
        foreach ($iterator as $key => $ColumnCell) {
            self::assertNotNull($ColumnCell);
            /** @scrutinizer ignore-call */
            $values[] = $ColumnCell->getValue();
            self::assertEquals($ColumnCellIndexResult++, $key);
            self::assertInstanceOf(Cell::class, $ColumnCell);
        }
        $transposed = array_map(/** @scrutinizer ignore-type */ null, ...self::CELL_VALUES);
        self::assertSame($transposed[0], $values);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIteratorStartEndRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnCellIterator($sheet, 'A', 2, 4);
        $ColumnCellIndexResult = 2;
        self::assertEquals($ColumnCellIndexResult, $iterator->key());

        $values = [];
        foreach ($iterator as $key => $ColumnCell) {
            self::assertNotNull($ColumnCell);
            /** @scrutinizer ignore-call */
            $values[] = $ColumnCell->getValue();
            self::assertEquals($ColumnCellIndexResult++, $key);
            self::assertInstanceOf(Cell::class, $ColumnCell);
        }
        self::assertSame([120, 130, 140], $values);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIteratorSeekAndPrev(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnCellIterator($sheet, 'A', 2, 4);
        $columnIndexResult = 4;
        $iterator->seek(4);
        self::assertEquals($columnIndexResult, $iterator->key());

        $values = [];
        while ($iterator->valid()) {
            $current = $iterator->current();
            self::assertNotNull($current);
            /** @scrutinizer ignore-call */
            $cell = $current->getCoordinate();
            $values[] = $sheet->getCell($cell)->getValue();
            $iterator->prev();
        }
        self::assertSame([140, 130, 120], $values);
        $spreadsheet->disconnectWorksheets();
    }

    public function testSeekOutOfRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $this->expectException(Except::class);
        $this->expectExceptionMessage('Row 1 is out of range');
        $iterator = new ColumnCellIterator($sheet, 'A', 2, 4);
        $iterator->seek(1);
        $spreadsheet->disconnectWorksheets();
    }

    public function testSeekNotExisting(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->expectException(Except::class);
        $this->expectExceptionMessage('Cell does not exist');

        $iterator = new ColumnCellIterator($sheet, 'A', 2, 4);
        $iterator->setIterateOnlyExistingCells(true);
        $iterator->seek(2);
    }

    public function testPrevOutOfRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnCellIterator($sheet, 'A', 2, 4);
        $iterator->prev();
        self::assertFalse($iterator->valid());
        $spreadsheet->disconnectWorksheets();
    }
}
