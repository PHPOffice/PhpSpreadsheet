<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as Except;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ColumnIteratorTest extends TestCase
{
    private const CELL_VALUES
        = [
            [110, 210, 310, 410, 510, 610],
            [120, 220, 320, 420, 520, 620],
            [130, null, 330, null, 530],
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
        $iterator = new ColumnIterator($sheet);
        $columnIndexResult = 'A';
        self::assertEquals($columnIndexResult, $iterator->key());

        $counter = 0;
        foreach ($iterator as $key => $column) {
            ++$counter;
            self::assertEquals($columnIndexResult++, $key);
            self::assertInstanceOf(Column::class, $column);
        }
        self::assertCount($counter, self::CELL_VALUES[0]);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIteratorStartEndRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnIterator($sheet, 'B', 'D');
        $columnIndexResult = 'B';
        self::assertEquals($columnIndexResult, $iterator->key());

        $counter = 0;
        foreach ($iterator as $key => $column) {
            ++$counter;
            self::assertEquals($columnIndexResult++, $key);
            self::assertInstanceOf(Column::class, $column);
        }
        self::assertSame(3, $counter);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIteratorSeekAndPrev(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnIterator($sheet, 'B', 'D');
        $columnIndexResult = 'D';
        $iterator->seek('D');
        self::assertEquals($columnIndexResult, $iterator->key());

        $counter = 0;
        while ($iterator->valid() !== false) {
            ++$counter;
            self::assertEquals($columnIndexResult, $iterator->key(), "counter $counter");
            // https://stackoverflow.com/questions/37027277/decrement-character-with-php
            $columnIndexResult = chr(ord($columnIndexResult) - 1);
            $iterator->prev();
        }
        self::assertSame(3, $counter);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIteratorResetStart(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnIterator($sheet, 'B', 'D');
        $iterator->resetStart('E');

        $key = $iterator->key();
        self::assertSame('E', $key);

        $lastColumn = $iterator->key();
        while ($iterator->valid() !== false) {
            $iterator->next();
            $lastColumn = $iterator->key();
        }
        self::assertSame('F', $lastColumn);
        $spreadsheet->disconnectWorksheets();
    }

    public function testSeekOutOfRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $this->expectException(Except::class);

        $iterator = new ColumnIterator($sheet, 'B', 'D');
        $iterator->seek('A');
        $spreadsheet->disconnectWorksheets();
    }

    public function testPrevOutOfRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnIterator($sheet, 'B', 'D');
        $iterator->prev();
        self::assertFalse($iterator->valid());
        $spreadsheet->disconnectWorksheets();
    }

    public function testResetStartOutOfRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $this->expectException(Except::class);

        $iterator = new ColumnIterator($sheet, 'B', 'D');
        $iterator->resetStart('H');
        $spreadsheet->disconnectWorksheets();
    }
}
