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
     * @param mixed $color
     */
    public function testGetRed($expectedResult, $color, ...$args): void
    {
        $result = Color::getRed($color, ...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerColorGetRed(): array
    {
        return require 'tests/data/Style/ColorGetRed.php';
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
        return require 'tests/data/Style/ColorGetGreen.php';
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

    public function providerColorChangeBrightness(): array
    {
        return require 'tests/data/Style/ColorChangeBrightness.php';
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
