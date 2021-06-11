<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Exception;
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

    public function testConvertDimensionInvalidUoM(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('pikachu is not a vaid unit of measure');
        (new Dimension('999'))->toUnit('pikachu');
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
            [60, '8.54', Dimension::UOM_PIXELS],
            [100, '100px', Dimension::UOM_PIXELS],
            [150, '200px', Dimension::UOM_POINTS],
            [45, '8.54', Dimension::UOM_POINTS],
            [12.5, '200px', Dimension::UOM_PICA],
            [3.75, '8.54', Dimension::UOM_PICA],
            [3.125, '300px', Dimension::UOM_INCHES],
            [0.625, '8.54', Dimension::UOM_INCHES],
            [7.9375, '300px', Dimension::UOM_CENTIMETERS],
            [1.5875, '8.54', Dimension::UOM_CENTIMETERS],
            [79.375, '300px', Dimension::UOM_MILLIMETERS],
            [15.875, '8.54', Dimension::UOM_MILLIMETERS],
        ];
    }
}
