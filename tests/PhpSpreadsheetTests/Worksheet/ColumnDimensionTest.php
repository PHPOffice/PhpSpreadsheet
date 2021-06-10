<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;
use PHPUnit\Framework\TestCase;

class ColumnDimensionTest extends TestCase
{
    public function testInstantiateColumnDimensionDefault(): void
    {
        $expected = 'A';
        $columnDimension = new ColumnDimension();
        self::assertInstanceOf(ColumnDimension::class, $columnDimension);
        $result = $columnDimension->getColumnIndex();
        self::assertEquals($expected, $result);
    }

    public function testGetAndSetColumnIndex(): void
    {
        $expected = 'B';
        $columnDimension = new ColumnDimension();
        $columnDimension->setColumnIndex($expected);
        $result = $columnDimension->getColumnIndex();
        self::assertSame($expected, $result);
    }

    public function testGetAndSetWidth(): void
    {
        $expected = 1.2;
        $columnDimension = new ColumnDimension();

        $columnDimension->setWidth($expected);
        $result = $columnDimension->getWidth();
        self::assertSame($expected, $result);

        $expectedPx = 32.0;
        $expectedPt = 24.0;
        $columnDimension->setWidth($expectedPx, 'px');
        $resultPx = $columnDimension->getWidth('px');
        self::assertSame($expectedPx, $resultPx);
        $resultPt = $columnDimension->getWidth('pt');
        self::assertSame($expectedPt, $resultPt);
    }

    public function testGetAndSetAutoSize(): void
    {
        $expected = true;
        $columnDimension = new ColumnDimension();
        $columnDimension->setAutoSize($expected);
        $result = $columnDimension->getAutoSize();
        self::assertTrue($result);
    }
}
