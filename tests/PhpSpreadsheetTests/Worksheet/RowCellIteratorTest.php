<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Exception as Except;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class RowCellIteratorTest extends TestCase
{
    private const CELL_VALUES
        = [
            [110, 120, 130, 140, 150, 160, 170],
            [210, 220, 230, 240, 250],
            [310, 320, 330, 340, 350, 360],
            [410, 420, 430, 440, 450],
            [510, 520, 530, 540, 550, 560],
            [610, 620, 630, 640, 650],
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
        $iterator = new RowCellIterator($sheet);
        $RowCellIndexResult = 'A';
        self::assertEquals($RowCellIndexResult, $iterator->key());

        $values = [];
        foreach ($iterator as $key => $RowCell) {
            self::assertNotNull($RowCell);
            $values[] = $RowCell->getValue();
            self::assertEquals($RowCellIndexResult++, $key);
            self::assertInstanceOf(Cell::class, $RowCell);
        }
        self::assertSame(self::CELL_VALUES[0], $values);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIteratorStartEndRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowCellIterator($sheet, 2, 'B', 'D');
        $RowCellIndexResult = 'B';
        self::assertEquals($RowCellIndexResult, $iterator->key());

        $values = [];
        foreach ($iterator as $key => $RowCell) {
            self::assertNotNull($RowCell);
            $values[] = $RowCell->getValue();
            self::assertEquals($RowCellIndexResult++, $key);
            self::assertInstanceOf(Cell::class, $RowCell);
        }
        self::assertSame([220, 230, 240], $values);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIteratorSeekAndPrev(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowCellIterator($sheet, 2, 'B', 'D');
        $RowCellIndexResult = 'D';
        $iterator->seek('D');
        self::assertEquals($RowCellIndexResult, $iterator->key());

        $values = [];
        while ($iterator->valid()) {
            $current = $iterator->current();
            self::assertNotNull($current);
            $cell = $current->getCoordinate();
            $values[] = $sheet->getCell($cell)->getValue();
            $iterator->prev();
        }
        self::assertSame([240, 230, 220], $values);
        $spreadsheet->disconnectWorksheets();
    }

    public function testSeekOutOfRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $this->expectException(Except::class);
        $this->expectExceptionMessage('Column A is out of range');

        $iterator = new RowCellIterator($sheet, 2, 'B', 'D');
        self::assertFalse($iterator->getIterateOnlyExistingCells());
        self::assertEquals(2, $iterator->getCurrentColumnIndex());
        $iterator->seek('A');
        $spreadsheet->disconnectWorksheets();
    }

    public function testSeekNotExisting(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $this->expectException(Except::class);
        $this->expectExceptionMessage('Cell does not exist');

        $iterator = new RowCellIterator($sheet, 2, 'B', 'D');
        $iterator->setIterateOnlyExistingCells(true);
        $iterator->seek('B');
        $spreadsheet->disconnectWorksheets();
    }

    public function testPrevOutOfRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowCellIterator($sheet, 2, 'B', 'D');
        $iterator->prev();
        self::assertFalse($iterator->valid());
        $spreadsheet->disconnectWorksheets();
    }
}
