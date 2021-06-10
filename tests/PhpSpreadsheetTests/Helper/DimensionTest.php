<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\Dimension;
use PHPUnit\Framework\TestCase;

class DimensionTest extends TestCase
{
    /**
     * @dataProvider providerCellWidth
     */
    public function testCreateDimension(float $expectedResult, string $dimension): void
    {
        $result = (new Dimension($dimension))->width();
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerConvertUoM
     */
    public function testConvertDimension(float $expectedResult, string $dimension, string $unitOfMeasure): void
    {
        $result = (new Dimension($dimension))->toUnit($unitOfMeasure);
        self::assertSame($expectedResult, $result);
    }

    public function providerCellWidth(): array
    {
        return [
            [12.0, '12'],
            [2.2852, '12pt'],
            [4.5703, '24 pt'],
            [5.1416, '36px'],
            [5.7129, '2.5pc'],
            [13.7109, '2.54cm'],
            [13.7109, '25.4mm'],
            [13.7109, '1in'],
            [4.27, '50%'],
            [3.7471, '3.2em'],
            [2.3419, '2ch'],
            [4.6838, '4ex'],
            [14.0515, '12rem'],
        ];
    }

    public function providerConvertUoM(): array
    {
        return [
            [60, '8.54', 'px'],
            [100, '100px', 'px'],
            [150, '200px', 'pt'],
            [45, '8.54', 'pt'],
            [12.5, '200px', 'pc'],
            [3.75, '8.54', 'pc'],
            [3.125, '300px', 'in'],
            [0.625, '8.54', 'in'],
            [7.9375, '300px', 'cm'],
            [1.5875, '8.54', 'cm'],
            [79.375, '300px', 'mm'],
            [15.875, '8.54', 'mm'],
        ];
    }
}
