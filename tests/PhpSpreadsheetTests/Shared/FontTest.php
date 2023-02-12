<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Style\Font as StyleFont;
use PHPUnit\Framework\TestCase;

class FontTest extends TestCase
{
    const FONT_PRECISION = 1.0E-12;

    public function testGetAutoSizeMethod(): void
    {
        $expectedResult = Font::AUTOSIZE_METHOD_APPROX;

        $result = Font::getAutoSizeMethod();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetAutoSizeMethod(): void
    {
        $autosizeMethodValues = [
            Font::AUTOSIZE_METHOD_EXACT,
            Font::AUTOSIZE_METHOD_APPROX,
        ];

        foreach ($autosizeMethodValues as $autosizeMethodValue) {
            $result = Font::setAutoSizeMethod($autosizeMethodValue);
            self::assertTrue($result);
        }
    }

    public function testSetAutoSizeMethodWithInvalidValue(): void
    {
        $unsupportedAutosizeMethod = 'guess';

        $result = Font::setAutoSizeMethod($unsupportedAutosizeMethod);
        self::assertFalse($result);
    }

    /**
     * @dataProvider providerFontSizeToPixels
     *
     * @param mixed $expectedResult
     * @param mixed $size
     */
    public function testFontSizeToPixels($expectedResult, $size): void
    {
        $result = Font::fontSizeToPixels($size);
        self::assertEquals($expectedResult, $result);
    }

    public function providerFontSizeToPixels(): array
    {
        return require 'tests/data/Shared/FontSizeToPixels.php';
    }

    /**
     * @dataProvider providerInchSizeToPixels
     *
     * @param mixed $expectedResult
     * @param mixed $size
     */
    public function testInchSizeToPixels($expectedResult, $size): void
    {
        $result = Font::inchSizeToPixels($size);
        self::assertEqualsWithDelta($expectedResult, $result, self::FONT_PRECISION);
    }

    public function providerInchSizeToPixels(): array
    {
        return require 'tests/data/Shared/InchSizeToPixels.php';
    }

    /**
     * @dataProvider providerCentimeterSizeToPixels
     *
     * @param mixed $expectedResult
     * @param mixed $size
     */
    public function testCentimeterSizeToPixels($expectedResult, $size): void
    {
        $result = Font::centimeterSizeToPixels($size);
        self::assertEqualsWithDelta($expectedResult, $result, self::FONT_PRECISION);
    }

    public function providerCentimeterSizeToPixels(): array
    {
        return require 'tests/data/Shared/CentimeterSizeToPixels.php';
    }

    public function testVerdanaRotation(): void
    {
        $font = new StyleFont();
        $font->setName('Verdana')->setSize(10);
        $width = Font::getTextWidthPixelsApprox('n', $font, 0);
        self::assertEquals(8, $width);
        $width = Font::getTextWidthPixelsApprox('n', $font, 45);
        self::assertEquals(7, $width);
        $width = Font::getTextWidthPixelsApprox('n', $font, -165);
        self::assertEquals(4, $width);
    }

    /**
     * @dataProvider providerCalculateApproximateColumnWidth
     */
    public function testCalculateApproximateColumnWidth(
        float $expectedWidth,
        StyleFont $font,
        string $text,
        int $rotation,
        StyleFont $defaultFont,
        bool $filter,
        int $indent
    ): void {
        $columnWidth = Font::calculateColumnWidth($font, $text, $rotation, $defaultFont, $filter, $indent);
        self::assertEquals($expectedWidth, $columnWidth);
    }

    public function providerCalculateApproximateColumnWidth(): array
    {
        return [
            [13.9966, new StyleFont(), 'Hello World', 0, new StyleFont(), false, 0],
            [16.2817, new StyleFont(), 'Hello World', 0, new StyleFont(), true, 0],
            [16.2817, new StyleFont(), 'Hello World', 0, new StyleFont(), false, 1],
            [18.7097, new StyleFont(), 'Hello World', 0, new StyleFont(), false, 2],
            [20.9949, new StyleFont(), 'Hello World', 0, new StyleFont(), false, 3],
            [6.9983, new StyleFont(), "Hello\nWorld", 0, new StyleFont(), false, 0],
            [9.2834, new StyleFont(), "Hello\nWorld", 0, new StyleFont(), true, 0],
            [17.5671, new StyleFont(), 'PhpSpreadsheet', 0, new StyleFont(), false, 0],
            [19.8523, new StyleFont(), 'PhpSpreadsheet', 0, new StyleFont(), false, 1],
        ];
    }
}
