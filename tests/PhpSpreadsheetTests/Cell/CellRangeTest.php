<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\CellRange;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class CellRangeTest extends TestCase
{
    public function testCreateCellRange(): void
    {
        $from = CellAddress::fromCellAddress('B5');
        $to = CellAddress::fromCellAddress('E2');
        $cellRange = new CellRange($from, $to);
        self::assertSame('B2:E5', (string) $cellRange);
    }

    public function testCreateCellRangeWithWorksheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle("Mark's Worksheet");

        $from = CellAddress::fromCellAddress('B5', $worksheet);
        $to = CellAddress::fromCellAddress('E2');
        $cellRange = new CellRange($from, $to);
        self::assertSame("'Mark''s Worksheet'!B2:E5", (string) $cellRange);
    }

    public function testCreateCellRangeWithWorksheets(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle("Mark's Worksheet");

        $from = CellAddress::fromCellAddress('B5', $worksheet);
        $to = CellAddress::fromCellAddress('E2', $worksheet);
        $cellRange = new CellRange($from, $to);
        self::assertSame("'Mark''s Worksheet'!B2:E5", (string) $cellRange);
    }

    public function testSingleCellRange(): void
    {
        $from = CellAddress::fromCellAddress('C3');
        $to = CellAddress::fromCellAddress('C3');
        $cellRange = new CellRange($from, $to);
        self::assertSame('C3', (string) $cellRange);
    }

    public function testSingleCellRangeWithWorksheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle("Mark's Worksheet");

        $from = CellAddress::fromCellAddress('C3', $worksheet);
        $to = CellAddress::fromCellAddress('C3');
        $cellRange = new CellRange($from, $to);
        self::assertSame("'Mark''s Worksheet'!C3", (string) $cellRange);
    }

    public function testRangeFrom(): void
    {
        $from = CellAddress::fromCellAddress('B5');
        $to = CellAddress::fromCellAddress('E2');
        $cellRange = new CellRange($from, $to);
        self::assertSame('B2', (string) $cellRange->from());
    }

    public function testRangeTo(): void
    {
        $from = CellAddress::fromCellAddress('B5');
        $to = CellAddress::fromCellAddress('E2');
        $cellRange = new CellRange($from, $to);
        self::assertSame('E5', (string) $cellRange->to());
    }

    public function testCreateCellRangeWithMismatchedWorksheets(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle("Mark's Worksheet");
        $secondWorksheet = new Worksheet($spreadsheet, 'A Second Worksheet');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('3d Cell Ranges are not supported');

        $from = CellAddress::fromCellAddress('B5', $worksheet);
        $to = CellAddress::fromCellAddress('E2', $secondWorksheet);
        new CellRange($from, $to);
    }

    public function testCreateCellRangeWithMismatchedSpreadsheets(): void
    {
        $spreadsheet1 = new Spreadsheet();
        $worksheet1 = $spreadsheet1->getActiveSheet();
        $worksheet1->setTitle("Mark's Worksheet");
        $spreadsheet2 = new Spreadsheet();
        $worksheet2 = $spreadsheet2->getActiveSheet();
        $worksheet2->setTitle("Mark's Worksheet");

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Worksheets must be in the same spreadsheet');

        $from = CellAddress::fromCellAddress('B5', $worksheet1);
        $to = CellAddress::fromCellAddress('E2', $worksheet2);
        new CellRange($from, $to);
    }

    public function testShiftRangeTo(): void
    {
        $from = CellAddress::fromCellAddress('B5');
        $to = CellAddress::fromCellAddress('E2');
        $cellRange = new CellRange($from, $to);
        self::assertSame('B2:E5', (string) $cellRange);

        $cellRange->to()
            ->nextColumn(2)
            ->nextRow(2);

        self::assertSame('B2', (string) $cellRange->from());
        self::assertSame('G7', (string) $cellRange->to());
        self::assertSame('B2:G7', (string) $cellRange);

        $cellRange->to()
            ->previousColumn()
            ->previousRow();

        self::assertSame('B2', (string) $cellRange->from());
        self::assertSame('F6', (string) $cellRange->to());
        self::assertSame('B2:F6', (string) $cellRange);
    }

    public function testShiftRangeFrom(): void
    {
        $from = CellAddress::fromCellAddress('B5');
        $to = CellAddress::fromCellAddress('E2');
        $cellRange = new CellRange($from, $to);
        self::assertSame('B2:E5', (string) $cellRange);

        $cellRange->from()
            ->nextColumn(5)
            ->nextRow(5);

        self::assertSame('E5', (string) $cellRange->from());
        self::assertSame('G7', (string) $cellRange->to());
        self::assertSame('E5:G7', (string) $cellRange);
    }
}
