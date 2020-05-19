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
    public function testGetRed($expectedResult, ...$args): void
    {
        $result = Color::getRed(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorGetRed()
    {
        return require 'tests/data/Style/ColorGetRed.php';
    }

    /**
     * @dataProvider providerColorGetGreen
     *
     * @param mixed $expectedResult
     */
    public function testGetGreen($expectedResult, ...$args): void
    {
        $result = Color::getGreen(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorGetGreen()
    {
        return require 'tests/data/Style/ColorGetGreen.php';
    }

    /**
     * @dataProvider providerColorGetBlue
     *
     * @param mixed $expectedResult
     */
    public function testGetBlue($expectedResult, ...$args): void
    {
        $result = Color::getBlue(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorGetBlue()
    {
        return require 'tests/data/Style/ColorGetBlue.php';
    }

    /**
     * @dataProvider providerColorChangeBrightness
     *
     * @param mixed $expectedResult
     */
    public function testChangeBrightness($expectedResult, ...$args): void
    {
        $result = Color::changeBrightness(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorChangeBrightness()
    {
        return require 'tests/data/Style/ColorChangeBrightness.php';
    }
}
