<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception as Except;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class RowIteratorTest extends TestCase
{
    private const CELL_VALUES
        = [
            [110, 120, 130],
            [210, 220],
            [310, 320, 330],
            [410, 420],
            [510, 520, 530],
            [610, 620],
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
        $iterator = new RowIterator($sheet);
        $rowIndexResult = 1;
        self::assertEquals($rowIndexResult, $iterator->key());

        $counter = 0;
        foreach ($iterator as $key => $row) {
            ++$counter;
            self::assertEquals($rowIndexResult++, $key);
            self::assertInstanceOf(Row::class, $row);
        }
        self::assertCount($counter, self::CELL_VALUES);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIteratorStartEndRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowIterator($sheet, 2, 4);
        $rowIndexResult = 2;
        self::assertEquals($rowIndexResult, $iterator->key());

        $counter = 0;
        foreach ($iterator as $key => $row) {
            ++$counter;
            self::assertEquals($rowIndexResult++, $key);
            self::assertInstanceOf(Row::class, $row);
        }
        self::assertSame(3, $counter);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIteratorSeekAndPrev(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowIterator($sheet, 2, 4);
        $columnIndexResult = 4;
        $iterator->seek(4);
        self::assertEquals($columnIndexResult, $iterator->key());

        $counter = 0;
        while ($iterator->valid() !== false) {
            ++$counter;
            self::assertEquals($columnIndexResult, $iterator->key());
            --$columnIndexResult;
            $iterator->prev();
        }
        self::assertSame(3, $counter);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIteratorResetStart(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowIterator($sheet, 2, 4);
        $iterator->resetStart(5);

        $key = $iterator->key();
        self::assertSame(5, $key);

        $lastRow = $iterator->key();
        while ($iterator->valid() !== false) {
            $iterator->next();
            $lastRow = $iterator->key();
        }
        self::assertSame(6, $lastRow);
        $spreadsheet->disconnectWorksheets();
    }

    public function testSeekOutOfRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $this->expectException(Except::class);

        $iterator = new RowIterator($sheet, 2, 4);
        $iterator->seek(1);
    }

    public function testPrevOutOfRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowIterator($sheet, 2, 4);
        $iterator->prev();
        self::assertFalse($iterator->valid());
    }

    public function testResetStartOutOfRange(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $this->expectException(Except::class);

        $iterator = new RowIterator($sheet, 2, 4);
        $iterator->resetStart(10);
    }
}
