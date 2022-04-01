<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\ColumnRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class ColumnRangeTest extends TestCase
{
    public function testCreateColumnRange(): void
    {
        $columnRange = new ColumnRange('C', 'E');
        self::assertSame('C', $columnRange->from());
        self::assertSame('E', $columnRange->to());
        self::assertSame(3, $columnRange->fromIndex());
        self::assertSame(5, $columnRange->toIndex());
        self::assertSame('C:E', (string) $columnRange);
        self::assertSame(3, $columnRange->columnCount());
        self::assertSame('C1:E1048576', (string) $columnRange->toCellRange());
    }

    public function testCreateSingleColumnRange(): void
    {
        $columnRange = new ColumnRange('E');
        self::assertSame('E', $columnRange->from());
        self::assertSame('E', $columnRange->to());
        self::assertSame('E:E', (string) $columnRange);
        self::assertSame(1, $columnRange->columnCount());
        self::assertSame('E1:E1048576', (string) $columnRange->toCellRange());
    }

    public function testCreateColumnRangeWithWorksheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle("Mark's Worksheet");

        $columnRange = new ColumnRange('C', 'E', $worksheet);
        self::assertSame('C', $columnRange->from());
        self::assertSame('E', $columnRange->to());
        self::assertSame("'Mark''s Worksheet'!C:E", (string) $columnRange);
        self::assertSame("'Mark''s Worksheet'!C1:E1048576", (string) $columnRange->toCellRange());
    }

    public function testCreateColumnRangeFromArray(): void
    {
        $columnRange = ColumnRange::fromArray(['C', 'E']);
        self::assertSame('C', $columnRange->from());
        self::assertSame('E', $columnRange->to());
        self::assertSame('C:E', (string) $columnRange);
        self::assertSame(3, $columnRange->columnCount());
        self::assertSame('C1:E1048576', (string) $columnRange->toCellRange());
    }

    public function testCreateColumnRangeFromIndexes(): void
    {
        $columnRange = ColumnRange::fromColumnIndexes(3, 5);
        self::assertSame('C', $columnRange->from());
        self::assertSame('E', $columnRange->to());
        self::assertSame('C:E', (string) $columnRange);
        self::assertSame(3, $columnRange->columnCount());
        self::assertSame('C1:E1048576', (string) $columnRange->toCellRange());
    }

    public function testColumnRangeNext(): void
    {
        $columnRange = new ColumnRange('C', 'E');
        $columnRangeNext = $columnRange->shiftDown(3);

        self::assertSame('F', $columnRangeNext->from());
        self::assertSame('H', $columnRangeNext->to());

        // Check that original Column Range isn't changed
        self::assertSame('C:E', (string) $columnRange);
    }

    public function testColumnRangePrevious(): void
    {
        $columnRange = new ColumnRange('C', 'E');
        $columnRangeNext = $columnRange->shiftUp();

        self::assertSame('B', $columnRangeNext->from());
        self::assertSame('D', $columnRangeNext->to());

        // Check that original Column Range isn't changed
        self::assertSame('C:E', (string) $columnRange);
    }
}
