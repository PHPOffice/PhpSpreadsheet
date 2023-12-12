<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\CellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ColumnIteratorEmptyTest extends TestCase
{
    private static function getPopulatedSheet(Spreadsheet $spreadsheet): Worksheet
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValueExplicit('A1', 'Hello World', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C2', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('D2', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('E2', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('E3', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('F2', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('F3', 'PHP', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('G2', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('G3', 'PHP', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('H2', null, DataType::TYPE_NULL);
        $sheet->setCellValueExplicit('H3', '', DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('H4', 'PHP', DataType::TYPE_STRING);

        return $sheet;
    }

    /**
     * @dataProvider emptyColumnBasic
     */
    public function testIteratorEmptyColumn(string $columnId, bool $expectedEmpty): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnIterator($sheet, 'A', 'I');
        $iterator->seek($columnId);
        $column = $iterator->current();
        $isEmpty = $column->isEmpty();
        self::assertSame($expectedEmpty, $isEmpty);
        $spreadsheet->disconnectWorksheets();
    }

    public static function emptyColumnBasic(): array
    {
        return [
            ['A', false],
            ['B', true],
            ['C', false],
            ['D', false],
            ['E', false],
            ['F', false],
            ['G', false],
            ['H', false],
            ['I', true],
        ];
    }

    /**
     * @dataProvider emptyColumnNullAsEmpty
     */
    public function testIteratorEmptyColumnWithNull(string $columnId, bool $expectedEmpty): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnIterator($sheet, 'A', 'I');
        $iterator->seek($columnId);
        $column = $iterator->current();
        $isEmpty = $column->isEmpty(CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL);
        self::assertSame($expectedEmpty, $isEmpty);
        $spreadsheet->disconnectWorksheets();
    }

    public static function emptyColumnNullAsEmpty(): array
    {
        return [
            ['A', false],
            ['B', true],
            ['C', true],
            ['D', false],
            ['E', false],
            ['F', false],
            ['G', false],
            ['H', false],
            ['I', true],
        ];
    }

    /**
     * @dataProvider emptyColumnEmptyStringAsEmpty
     */
    public function testIteratorEmptyColumnWithEmptyString(string $columnId, bool $expectedEmpty): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnIterator($sheet, 'A', 'I');
        $iterator->seek($columnId);
        $column = $iterator->current();
        $isEmpty = $column->isEmpty(CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL);
        self::assertSame($expectedEmpty, $isEmpty);
        $spreadsheet->disconnectWorksheets();
    }

    public static function emptyColumnEmptyStringAsEmpty(): array
    {
        return [
            ['A', false],
            ['B', true],
            ['C', false],
            ['D', true],
            ['E', false],
            ['F', false],
            ['G', false],
            ['H', false],
            ['I', true],
        ];
    }

    /**
     * @dataProvider emptyColumnNullAndEmptyStringAsEmpty
     */
    public function testIteratorEmptyColumnWithNullAndEmptyString(string $columnId, bool $expectedEmpty): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $iterator = new ColumnIterator($sheet, 'A', 'I');
        $iterator->seek($columnId);
        $column = $iterator->current();
        $isEmpty = $column->isEmpty(
            CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL | CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL
        );
        self::assertSame($expectedEmpty, $isEmpty);
        $spreadsheet->disconnectWorksheets();
    }

    public static function emptyColumnNullAndEmptyStringAsEmpty(): array
    {
        return [
            ['A', false],
            ['B', true],
            ['C', true],
            ['D', true],
            ['E', true],
            ['F', false],
            ['G', false],
            ['H', false],
            ['I', true],
        ];
    }

    public function testIteratorEmptyColumnWithRowLimit(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = self::getPopulatedSheet($spreadsheet);
        $sheet->setCellValue('A8', 'NO LONGER EMPTY');

        $iterator = new ColumnIterator($sheet, 'A', 'A');
        $column = $iterator->current();

        $isEmpty = $column->isEmpty(CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL);
        self::assertFalse($isEmpty);
        $isEmpty = $column->isEmpty(CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL, 2, 7);
        self::assertTrue($isEmpty);

        $spreadsheet->disconnectWorksheets();
    }
}
