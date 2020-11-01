<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Style\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function testNewColor()
    {
        $color = new Color('FF123456');
        self::assertEquals('FF123456', $color->getARGB());
        self::assertEquals('123456', $color->getRGB());
    }

    public function testARGBSetter()
    {
        $color = new Color();
        $color->setARGB('80123456');
        self::assertEquals('80123456', $color->getARGB());
        self::assertEquals('123456', $color->getRGB());
    }

    public function testARGBSetterEmpty()
    {
        $color = new Color();
        $color->setARGB();
        self::assertEquals(Color::COLOR_BLACK, $color->getARGB());
    }

    public function testARGBSetterInvalid()
    {
        $color = new Color('80123456');
        $color->setARGB('INVALID COLOR');
        self::assertEquals('80123456', $color->getARGB());
    }

    public function testRGBSetter()
    {
        $color = new Color();
        $color->setRGB('123456');
        self::assertEquals('123456', $color->getRGB());
        self::assertEquals('FF123456', $color->getARGB());
    }

    public function testRGBSetterEmpty()
    {
        $color = new Color();
        $color->setRGB();
        self::assertEquals(Color::COLOR_BLACK, $color->getARGB());
    }

    public function testRGBSetterInvalid()
    {
        $color = new Color('80123456');
        $color->setRGB('INVALID COLOR');
        self::assertEquals('123456', $color->getRGB());
    }

    public function testARGBFromArray()
    {
        $color = new Color();
        $color->applyFromArray(['argb' => '80123456']);
        self::assertEquals('80123456', $color->getARGB());
        self::assertEquals('123456', $color->getRGB());
    }

    public function testRGBFromArray()
    {
        $color = new Color();
        $color->applyFromArray(['rgb' => '123456']);
        self::assertEquals('123456', $color->getRGB());
        self::assertEquals('FF123456', $color->getARGB());
    }

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
        return require 'tests/data/Style/Color/ColorGetRed.php';
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
        return require 'tests/data/Style/Color/ColorGetGreen.php';
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
        return require 'tests/data/Style/Color/ColorGetBlue.php';
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
        return require 'tests/data/Style/Color/ColorChangeBrightness.php';
    }
}
