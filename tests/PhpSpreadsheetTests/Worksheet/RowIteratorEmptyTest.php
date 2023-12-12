<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\CellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class RowIteratorEmptyTest extends TestCase
{
    private static function getPopulatedSheet(Spreadsheet $spreadsheet): Worksheet
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValueExplicit('A1', 'Hello World', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B3', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('B4', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B5', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('C5', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B6', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('C6', 'PHP', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B7', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C7', 'PHP', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('B8', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('C8', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('D8', 'PHP', DataType::TYPE_STRING);

        return $sheet;
    }

    /**
     * @dataProvider emptyRowBasic
     */
    public function testIteratorEmptyRow(int $rowId, bool $expectedEmpty): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowIterator($sheet, 1, 9);
        $iterator->seek($rowId);
        $row = $iterator->current();

        $isEmpty = $row->isEmpty();
        self::assertSame($expectedEmpty, $isEmpty);

        $spreadsheet->disconnectWorksheets();
    }

    public static function emptyRowBasic(): array
    {
        return [
            [1, false],
            [2, true],
            [3, false],
            [4, false],
            [5, false],
            [6, false],
            [7, false],
            [8, false],
            [9, true],
        ];
    }

    /**
     * @dataProvider emptyRowNullAsEmpty
     */
    public function testIteratorEmptyRowWithNull(int $rowId, bool $expectedEmpty): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowIterator($sheet, 1, 9);
        $iterator->seek($rowId);
        $row = $iterator->current();
        $isEmpty = $row->isEmpty(CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL);
        self::assertSame($expectedEmpty, $isEmpty);
        $spreadsheet->disconnectWorksheets();
    }

    public static function emptyRowNullAsEmpty(): array
    {
        return [
            [1, false],
            [2, true],
            [3, true],
            [4, false],
            [5, false],
            [6, false],
            [7, false],
            [8, false],
            [9, true],
        ];
    }

    /**
     * @dataProvider emptyRowEmptyStringAsEmpty
     */
    public function testIteratorEmptyRowWithEmptyString(int $rowId, bool $expectedEmpty): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowIterator($sheet, 1, 9);
        $iterator->seek($rowId);
        $row = $iterator->current();
        $isEmpty = $row->isEmpty(CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL);
        self::assertSame($expectedEmpty, $isEmpty);
        $spreadsheet->disconnectWorksheets();
    }

    public static function emptyRowEmptyStringAsEmpty(): array
    {
        return [
            [1, false],
            [2, true],
            [3, false],
            [4, true],
            [5, false],
            [6, false],
            [7, false],
            [8, false],
            [9, true],
        ];
    }

    /**
     * @dataProvider emptyRowNullAndEmptyStringAsEmpty
     */
    public function testIteratorEmptyRowWithNullAndEmptyString(int $rowId, bool $expectedEmpty): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new RowIterator($sheet, 1, 9);
        $iterator->seek($rowId);
        $row = $iterator->current();
        $isEmpty = $row->isEmpty(
            CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL | CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL
        );
        self::assertSame($expectedEmpty, $isEmpty);
        $spreadsheet->disconnectWorksheets();
    }

    public static function emptyRowNullAndEmptyStringAsEmpty(): array
    {
        return [
            [1, false],
            [2, true],
            [3, true],
            [4, true],
            [5, true],
            [6, false],
            [7, false],
            [8, false],
            [9, true],
        ];
    }

    public function testIteratorEmptyRowWithColumnLimit(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $sheet->setCellValue('E3', 'NO LONGER EMPTY');

        $iterator = new RowIterator($sheet, 3, 3);
        $row = $iterator->current();

        $isEmpty = $row->isEmpty(CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL);
        self::assertFalse($isEmpty);
        $isEmpty = $row->isEmpty(CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL, 'A', 'D');
        self::assertTrue($isEmpty);

        $spreadsheet->disconnectWorksheets();
    }
}
