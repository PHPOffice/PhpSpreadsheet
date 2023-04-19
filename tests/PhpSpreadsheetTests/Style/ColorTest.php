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
     */
    public function testGetRed($expectedResult, string $color, ?bool $bool = null): void
    {
        if ($bool === null) {
            $result = Color::getRed($color);
        } else {
            $result = Color::getRed($color, $bool);
        }
        self::assertEquals($expectedResult, $result);
    }

    public static function providerColorGetRed(): array
    {
        return require 'tests/data/Style/Color/ColorGetRed.php';
    }

    /**
     * @dataProvider providerColorGetGreen
     *
     * @param mixed $expectedResult
     */
    public function testGetGreen($expectedResult, string $color, ?bool $bool = null): void
    {
        if ($bool === null) {
            $result = Color::getGreen($color);
        } else {
            $result = Color::getGreen($color, $bool);
        }
        self::assertEquals($expectedResult, $result);
    }

    public static function providerColorGetGreen(): array
    {
        return require 'tests/data/Style/Color/ColorGetGreen.php';
    }

    /**
     * @dataProvider providerColorGetBlue
     *
     * @param mixed $expectedResult
     */
    public function testGetBlue($expectedResult, string $color, ?bool $bool = null): void
    {
        if ($bool === null) {
            $result = Color::getBlue($color);
        } else {
            $result = Color::getBlue($color, $bool);
        }
        self::assertEquals($expectedResult, $result);
    }

    public static function providerColorGetBlue(): array
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

    public static function providerColorChangeBrightness(): array
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

    public function testNamedColors(): void
    {
        $color = new Color();
        $color->setARGB('Blue');
        self::assertEquals(Color::COLOR_BLUE, $color->getARGB());
        $color->setARGB('black');
        self::assertEquals(Color::COLOR_BLACK, $color->getARGB());
        $color->setARGB('wHite');
        self::assertEquals(Color::COLOR_WHITE, $color->getARGB());
        $color->setRGB('reD');
        self::assertEquals(Color::COLOR_RED, $color->getARGB());
        $color->setRGB('GREEN');
        self::assertEquals(Color::COLOR_GREEN, $color->getARGB());
        $color->setRGB('magenta');
        self::assertEquals(Color::COLOR_MAGENTA, $color->getARGB());
        $color->setRGB('YeLlOw');
        self::assertEquals(Color::COLOR_YELLOW, $color->getARGB());
        $color->setRGB('CYAN');
        self::assertEquals(Color::COLOR_CYAN, $color->getARGB());
        $color->setRGB('123456ab');
        self::assertEquals('123456ab', $color->getARGB());
        self::assertEquals('3456ab', $color->getRGB());
        $color->setARGB('3456cd');
        self::assertEquals('FF3456cd', $color->getARGB());
        self::assertEquals('3456cd', $color->getRGB());
    }
}
