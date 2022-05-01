<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\RowRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class RowRangeTest extends TestCase
{
    public function testCreateRowRange(): void
    {
        $rowRange = new RowRange(3, 5);
        self::assertSame(3, $rowRange->from());
        self::assertSame(5, $rowRange->to());
        self::assertSame('3:5', (string) $rowRange);
        self::assertSame(3, $rowRange->rowCount());
        self::assertSame('A3:XFD5', (string) $rowRange->toCellRange());
    }

    public function testCreateSingleRowRange(): void
    {
        $rowRange = new RowRange(3);
        self::assertSame(3, $rowRange->from());
        self::assertSame(3, $rowRange->to());
        self::assertSame('3:3', (string) $rowRange);
        self::assertSame(1, $rowRange->rowCount());
    }

    public function testCreateRowRangeWithWorksheet(): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle("Mark's Worksheet");

        $rowRange = new RowRange(3, 5, $worksheet);
        self::assertSame(3, $rowRange->from());
        self::assertSame(5, $rowRange->to());
        self::assertSame("'Mark''s Worksheet'!3:5", (string) $rowRange);
    }

    public function testCreateRowRangeFromArray(): void
    {
        $rowRange = RowRange::fromArray([3, 5]);
        self::assertSame(3, $rowRange->from());
        self::assertSame(5, $rowRange->to());
        self::assertSame('3:5', (string) $rowRange);
        self::assertSame(3, $rowRange->rowCount());
        self::assertSame('A3:XFD5', (string) $rowRange->toCellRange());
    }

    public function testRowRangeNext(): void
    {
        $rowRange = new RowRange(3, 5);
        $rowRangeNext = $rowRange->shiftRight(3);

        self::assertSame(6, $rowRangeNext->from());
        self::assertSame(8, $rowRangeNext->to());

        // Check that original Row Range isn't changed
        self::assertSame('3:5', (string) $rowRange);
    }

    public function testRowRangePrevious(): void
    {
        $rowRange = new RowRange(3, 5);
        $rowRangeNext = $rowRange->shiftLeft();

        self::assertSame(2, $rowRangeNext->from());
        self::assertSame(4, $rowRangeNext->to());

        // Check that original Row Range isn't changed
        self::assertSame('3:5', (string) $rowRange);
    }
}
