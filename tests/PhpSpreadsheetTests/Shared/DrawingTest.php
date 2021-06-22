<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PHPUnit\Framework\TestCase;

class DrawingTest extends TestCase
{
    /**
     * @dataProvider providerPixelsToCellDimension
     */
    public function testPixelsToCellDimension(
        float $expectedResult,
        int $pixelSize,
        string $fontName,
        int $fontSize
    ): void {
        $font = new Font();
        $font->setName($fontName);
        $font->setSize($fontSize);

        $result = Drawing::pixelsToCellDimension($pixelSize, $font);
        self::assertSame($expectedResult, $result);
    }

    /**
     * @dataProvider providerCellDimensionToPixels
     */
    public function testCellDimensionToPixels(
        int $expectedResult,
        int $cellSize,
        string $fontName,
        int $fontSize
    ): void {
        $font = new Font();
        $font->setName($fontName);
        $font->setSize($fontSize);

        $result = Drawing::cellDimensionToPixels($cellSize, $font);
        self::assertSame($expectedResult, $result);
    }

    public function providerPixelsToCellDimension(): array
    {
        return [
            [19.9951171875, 100, 'Arial', 7],
            [14.2822265625, 100, 'Arial', 9],
            [14.2822265625, 100, 'Arial', 11],
            [13.092041015625, 100, 'Arial', 12], // approximation by extrapolating from Calibri 11
            [19.9951171875, 100, 'Calibri', 7],
            [16.664341517857142, 100, 'Calibri', 9],
            [14.2822265625, 100, 'Calibri', 11],
            [13.092041015625, 100, 'Calibri', 12], // approximation by extrapolating from Calibri 11
            [19.9951171875, 100, 'Verdana', 7],
            [12.5, 100, 'Verdana', 9],
            [13.092041015625, 100, 'Verdana', 12], // approximation by extrapolating from Calibri 11
            [17.4560546875, 100, 'Wingdings', 9], // approximation by extrapolating from Calibri 11
        ];
    }

    public function providerCellDimensionToPixels(): array
    {
        return [
            [500, 100, 'Arial', 7],
            [700, 100, 'Arial', 9],
            [700, 100, 'Arial', 11],
            [764, 100, 'Arial', 12], // approximation by extrapolating from Calibri 11
            [500, 100, 'Calibri', 7],
            [600, 100, 'Calibri', 9],
            [700, 100, 'Calibri', 11],
            [764, 100, 'Calibri', 12], // approximation by extrapolating from Calibri 11
            [500, 100, 'Verdana', 7],
            [800, 100, 'Verdana', 9],
            [764, 100, 'Verdana', 12], // approximation by extrapolating from Calibri 11
            [573, 100, 'Wingdings', 9], // approximation by extrapolating from Calibri 11
        ];
    }
}
