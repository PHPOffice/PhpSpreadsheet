<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\Font;
use PHPUnit\Framework\TestCase;

class FontTest extends TestCase
{
    public function testGetAutoSizeMethod()
    {
        $expectedResult = Font::AUTOSIZE_METHOD_APPROX;

        $result = Font::getAutoSizeMethod();
        self::assertEquals($expectedResult, $result);
    }

    public function testSetAutoSizeMethod()
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

    public function testSetAutoSizeMethodWithInvalidValue()
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
    public function testFontSizeToPixels($expectedResult, ...$args)
    {
        $result = Font::fontSizeToPixels(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerFontSizeToPixels()
    {
        return require 'data/Shared/FontSizeToPixels.php';
    }

    /**
     * @dataProvider providerInchSizeToPixels
     *
     * @param mixed $expectedResult
     */
    public function testInchSizeToPixels($expectedResult, ...$args)
    {
        $result = Font::inchSizeToPixels(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerInchSizeToPixels()
    {
        return require 'data/Shared/InchSizeToPixels.php';
    }

    /**
     * @dataProvider providerCentimeterSizeToPixels
     *
     * @param mixed $expectedResult
     */
    public function testCentimeterSizeToPixels($expectedResult, ...$args)
    {
        $result = Font::centimeterSizeToPixels(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCentimeterSizeToPixels()
    {
        return require 'data/Shared/CentimeterSizeToPixels.php';
    }
}
