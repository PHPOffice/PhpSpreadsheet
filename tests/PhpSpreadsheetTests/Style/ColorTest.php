<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Style\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    /**
     * @dataProvider providerColorGetRed
     *
     * @param mixed $expectedResult
     */
    public function testGetRed($expectedResult, ...$args)
    {
        $result = Color::getRed(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorGetRed()
    {
        return require 'data/Style/ColorGetRed.php';
    }

    /**
     * @dataProvider providerColorGetGreen
     *
     * @param mixed $expectedResult
     */
    public function testGetGreen($expectedResult, ...$args)
    {
        $result = Color::getGreen(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorGetGreen()
    {
        return require 'data/Style/ColorGetGreen.php';
    }

    /**
     * @dataProvider providerColorGetBlue
     *
     * @param mixed $expectedResult
     */
    public function testGetBlue($expectedResult, ...$args)
    {
        $result = Color::getBlue(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorGetBlue()
    {
        return require 'data/Style/ColorGetBlue.php';
    }

    /**
     * @dataProvider providerColorChangeBrightness
     *
     * @param mixed $expectedResult
     */
    public function testChangeBrightness($expectedResult, ...$args)
    {
        $result = Color::changeBrightness(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorChangeBrightness()
    {
        return require 'data/Style/ColorChangeBrightness.php';
    }
}
