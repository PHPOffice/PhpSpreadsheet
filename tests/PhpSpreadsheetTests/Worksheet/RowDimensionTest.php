<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\RowDimension;
use PHPUnit\Framework\TestCase;

class RowDimensionTest extends TestCase
{
    public function testInstantiateColumnDimensionDefault(): void
    {
        $expected = 0;
        $rowDimension = new RowDimension();
        self::assertInstanceOf(RowDimension::class, $rowDimension);
        $result = $rowDimension->getRowIndex();
        self::assertEquals($expected, $result);
    }

    public function testGetAndSetColumnIndex(): void
    {
        $expected = 2;
        $rowDimension = new RowDimension();
        $rowDimension->setRowIndex($expected);
        $result = $rowDimension->getRowIndex();
        self::assertSame($expected, $result);
    }

    public function testGetAndSetHeight(): void
    {
        $expected = 1.2;
        $columnDimension = new RowDimension();

        $columnDimension->setRowHeight($expected);
        $result = $columnDimension->getRowHeight();
        self::assertSame($expected, $result);

        $expectedPx = 32.0;
        $expectedPt = 24.0;
        $columnDimension->setRowHeight($expectedPx, 'px');
        $resultPx = $columnDimension->getRowHeight('px');
        self::assertSame($expectedPx, $resultPx);
        $resultPt = $columnDimension->getRowHeight('pt');
        self::assertSame($expectedPt, $resultPt);
    }

    public function testRowZeroHeight(): void
    {
        $expected = true;
        $rowDimension = new RowDimension();
        $rowDimension->setZeroHeight($expected);
        $result = $rowDimension->getZeroHeight();
        self::assertSame($expected, $result);
    }
}
