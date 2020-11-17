<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Style\Font as StyleFont;
use PHPUnit\Framework\TestCase;

class FontTest extends TestCase
{
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
     */
    public function testFontSizeToPixels($expectedResult, ...$args): void
    {
        $result = Font::fontSizeToPixels(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerFontSizeToPixels()
    {
        return require 'tests/data/Shared/FontSizeToPixels.php';
    }

    /**
     * @dataProvider providerInchSizeToPixels
     *
     * @param mixed $expectedResult
     */
    public function testInchSizeToPixels($expectedResult, ...$args): void
    {
        $result = Font::inchSizeToPixels(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerInchSizeToPixels()
    {
        return require 'tests/data/Shared/InchSizeToPixels.php';
    }

    /**
     * @dataProvider providerCentimeterSizeToPixels
     *
     * @param mixed $expectedResult
     */
    public function testCentimeterSizeToPixels($expectedResult, ...$args): void
    {
        $result = Font::centimeterSizeToPixels(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCentimeterSizeToPixels()
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
}
