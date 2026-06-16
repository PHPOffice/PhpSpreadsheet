<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PrintAreaTest extends TestCase
{
    #[DataProvider('removeRowsProvider')]
    public function testRemoveRows(string $expected, int $rowNumber, int $numberOfRows): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setPrintArea('B5:M41');
        $sheet->removeRow($rowNumber, $numberOfRows);
        self::assertSame($expected, $sheet->getPageSetup()->getPrintArea());
        $spreadsheet->disconnectWorksheets();
    }

    public static function removeRowsProvider(): array
    {
        return [
            'before beginning of printArea' => ['B3:M39', 3, 2],
            'creep into printArea' => ['B3:M37', 3, 4],
            'entirely within printArea' => ['B5:M36', 6, 5],
            'creep past end of printArea' => ['B5:M34', 35, 8],
            'after end of printArea' => ['B5:M41', 55, 8],
            'entire printArea' => ['', 5, 37],
            'entire printArea+' => ['', 4, 47],
        ];
    }

    #[DataProvider('removeColumnsProvider')]
    public function testRemoveColumns(string $expected, string $column, int $numberOfColumns): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setPrintArea('D5:M41');
        $sheet->removeColumn($column, $numberOfColumns);
        self::assertSame($expected, $sheet->getPageSetup()->getPrintArea());
        $spreadsheet->disconnectWorksheets();
    }

    public static function removeColumnsProvider(): array
    {
        return [
            'before beginning of printArea' => ['B5:K41', 'B', 2],
            'creep into printArea' => ['B5:I41', 'B', 4],
            'entirely within printArea' => ['D5:I41', 'E', 4],
            'creep past end of printArea' => ['D5:K41', 'L', 3],
            'after end of printArea' => ['D5:M41', 'P', 8],
            'entire printArea' => ['', 'D', 10],
            'entire printArea+' => ['', 'C', 15],
        ];
    }

    #[DataProvider('addRowsProvider')]
    public function testAddRows(string $expected, int $rowNumber, int $numberOfRows): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setPrintArea('D5:M41');
        $sheet->insertNewRowBefore($rowNumber, $numberOfRows);
        self::assertSame($expected, $sheet->getPageSetup()->getPrintArea());
        $spreadsheet->disconnectWorksheets();
    }

    public static function addRowsProvider(): array
    {
        return [
            'entirely within printArea' => ['D5:M44', 15, 3],
            'above printArea' => ['D9:M45', 3, 4],
            'below printArea' => ['D5:M41', 48, 4],
        ];
    }

    #[DataProvider('addColumnsProvider')]
    public function testAddColumns(string $expected, string $column, int $numberOfColumns): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setPrintArea('D5:M41');
        $sheet->insertNewColumnBefore($column, $numberOfColumns);
        self::assertSame($expected, $sheet->getPageSetup()->getPrintArea());
        $spreadsheet->disconnectWorksheets();
    }

    public static function addColumnsProvider(): array
    {
        return [
            'entirely within printArea' => ['D5:P41', 'J', 3],
            'left of printArea' => ['H5:Q41', 'C', 4],
            'right of printArea' => ['D5:M41', 'Q', 4],
        ];
    }
}
