<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Style\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function testNewColor(): void
    {
        $color = new Color('FF123456');
        self::assertEquals('FF123456', $color->getARGB());
        self::assertEquals('123456', $color->getRGB());
    }

    public function testARGBSetter(): void
    {
        $color = new Color();
        $color->setARGB('80123456');
        self::assertEquals('80123456', $color->getARGB());
        self::assertEquals('123456', $color->getRGB());
    }

    public function testARGBSetterEmpty(): void
    {
        $color = new Color();
        $color->setARGB();
        self::assertEquals(Color::COLOR_BLACK, $color->getARGB());
    }

    public function testARGBSetterInvalid(): void
    {
        $color = new Color('80123456');
        $color->setARGB('INVALID COLOR');
        self::assertEquals('80123456', $color->getARGB());
    }

    public function testRGBSetter(): void
    {
        $color = new Color();
        $color->setRGB('123456');
        self::assertEquals('123456', $color->getRGB());
        self::assertEquals('FF123456', $color->getARGB());
    }

    public function testRGBSetterEmpty(): void
    {
        $color = new Color();
        $color->setRGB();
        self::assertEquals(Color::COLOR_BLACK, $color->getARGB());
    }

    public function testRGBSetterInvalid(): void
    {
        $color = new Color('80123456');
        $color->setRGB('INVALID COLOR');
        self::assertEquals('123456', $color->getRGB());
    }

    public function testARGBFromArray(): void
    {
        $color = new Color();
        $color->applyFromArray(['argb' => '80123456']);
        self::assertEquals('80123456', $color->getARGB());
        self::assertEquals('123456', $color->getRGB());
    }

    public function testRGBFromArray(): void
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
     * @param mixed $color
     */
    public function testGetRed($expectedResult, $color, ...$args): void
    {
        $result = Color::getRed($color, ...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorGetRed(): array
    {
        return require 'tests/data/Style/Color/ColorGetRed.php';
    }

    /**
     * @dataProvider providerColorGetGreen
     *
     * @param mixed $expectedResult
     * @param mixed $color
     */
    public function testGetGreen($expectedResult, $color, ...$args): void
    {
        $result = Color::getGreen($color, ...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorGetGreen(): array
    {
        return require 'tests/data/Style/Color/ColorGetGreen.php';
    }

    /**
     * @dataProvider providerColorGetBlue
     *
     * @param mixed $expectedResult
     * @param mixed $color
     */
    public function testGetBlue($expectedResult, $color, ...$args): void
    {
        $result = Color::getBlue($color, ...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorGetBlue(): array
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

    public function providerColorChangeBrightness(): array
    {
        return require 'tests/data/Style/Color/ColorChangeBrightness.php';
    }

    public function testDefaultColor(): void
    {
        $color = new Color();
        $color->setARGB('FFFF0000');
        self::assertEquals('FFFF0000', $color->getARGB());
        self::assertEquals('FF0000', $color->getRGB());
        $color->setARGB('');
        self::assertEquals(Color::COLOR_BLACK, $color->getARGB());
        self::assertEquals('000000', $color->getRGB());
        $color->setARGB('FFFF0000');
        self::assertEquals('FFFF0000', $color->getARGB());
        self::assertEquals('FF0000', $color->getRGB());
        $color->setRGB('');
        self::assertEquals(Color::COLOR_BLACK, $color->getARGB());
        self::assertEquals('000000', $color->getRGB());
    }
}
