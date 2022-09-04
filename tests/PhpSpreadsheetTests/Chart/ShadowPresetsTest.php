<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\ChartColor;
use PhpOffice\PhpSpreadsheet\Chart\GridLines;
use PhpOffice\PhpSpreadsheet\Chart\Properties;
use PHPUnit\Framework\TestCase;

class ShadowPresetsTest extends TestCase
{
    public function testGridlineShadowPresets(): void
    {
        $gridlines = new GridLines();
        $gridlines->setShadowProperties(17);
        $expectedShadow = [
            'effect' => 'innerShdw',
            'distance' => 4,
            'direction' => 270,
            'blur' => 5,
        ];
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($gridlines->getShadowProperty($key), $value, $key);
        }
    }

    public function testGridlineShadowPresetsWithArray(): void
    {
        $gridlines = new GridLines();
        $gridlines->setShadowProperties(20);
        $expectedShadow = [
            'effect' => 'outerShdw',
            'blur' => 6,
            'direction' => 315,
            'size' => [
                'sx' => null,
                'sy' => 0.23,
                'kx' => -20,
                'ky' => null,
            ],
            'algn' => 'bl',
            'rotWithShape' => '0',
        ];
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($gridlines->getShadowProperty($key), $value, $key);
        }
    }

    public function testAxisShadowPresets(): void
    {
        $axis = new Axis();
        $axis->setShadowProperties(9);
        $expectedShadow = [
            'effect' => 'outerShdw',
            'blur' => 4,
            'distance' => 3,
            'direction' => 225,
            'algn' => 'br',
            'rotWithShape' => '0',
        ];
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($axis->getShadowProperty($key), $value, $key);
        }
    }

    public function testAxisShadowPresetsWithChanges(): void
    {
        $axis = new Axis();
        $axis->setShadowProperties(
            9, // preset
            'FF0000', // colorValue
            'srgbClr', // colorType
            20, // alpha
            6, // blur
            30, // direction
            4, // distance
        );
        $expectedShadow = [
            'effect' => 'outerShdw',
            'blur' => 6,
            'distance' => 4,
            'direction' => 30,
            'algn' => 'br',
            'rotWithShape' => '0',
            'color' => [
                'value' => 'FF0000',
                'type' => 'srgbClr',
                'alpha' => 20,
            ],
        ];
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($axis->getShadowProperty($key), $value, $key);
        }
    }

    public function testGridlinesShadowPresetsWithChanges(): void
    {
        $gridline = new GridLines();
        $gridline->setShadowProperties(
            9, // preset
            'FF0000', // colorValue
            'srgbClr', // colorType
            20, // alpha
            6, // blur
            30, // direction
            4, // distance
        );
        $expectedShadow = [
            'effect' => 'outerShdw',
            'blur' => 6,
            'distance' => 4,
            'direction' => 30,
            'algn' => 'br',
            'rotWithShape' => '0',
            'color' => [
                'value' => 'FF0000',
                'type' => 'srgbClr',
                'alpha' => 20,
            ],
        ];
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($gridline->getShadowProperty($key), $value, $key);
        }
    }

    public function testPreset0(): void
    {
        $axis = new Axis();
        $axis->setShadowProperties(0);
        $expectedShadow = [
            'presets' => Properties::SHADOW_PRESETS_NOSHADOW,
            'effect' => null,
            'color' => [
                'type' => ChartColor::EXCEL_COLOR_TYPE_STANDARD,
                'value' => 'black',
                'alpha' => 40,
            ],
            'size' => [
                'sx' => null,
                'sy' => null,
                'kx' => null,
                'ky' => null,
            ],
            'blur' => null,
            'direction' => null,
            'distance' => null,
            'algn' => null,
            'rotWithShape' => null,
        ];
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($value, $axis->getShadowProperty($key), $key);
        }
    }

    public function testOutOfRangePresets(): void
    {
        $axis = new Axis();
        $axis->setShadowProperties(99);
        $expectedShadow = [
            'presets' => Properties::SHADOW_PRESETS_NOSHADOW,
            'effect' => null,
            'color' => [
                'type' => ChartColor::EXCEL_COLOR_TYPE_STANDARD,
                'value' => 'black',
                'alpha' => 40,
            ],
            'size' => [
                'sx' => null,
                'sy' => null,
                'kx' => null,
                'ky' => null,
            ],
            'blur' => null,
            'direction' => null,
            'distance' => null,
            'algn' => null,
            'rotWithShape' => null,
        ];
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($value, $axis->getShadowProperty($key), $key);
        }
    }

    public function testOutOfRangeGridlines(): void
    {
        $gridline = new GridLines();
        $gridline->setShadowProperties(99);
        $expectedShadow = [
            'presets' => Properties::SHADOW_PRESETS_NOSHADOW,
            'effect' => null,
            'color' => [
                'type' => ChartColor::EXCEL_COLOR_TYPE_STANDARD,
                'value' => 'black',
                'alpha' => 40,
            ],
            'size' => [
                'sx' => null,
                'sy' => null,
                'kx' => null,
                'ky' => null,
            ],
            'blur' => null,
            'direction' => null,
            'distance' => null,
            'algn' => null,
            'rotWithShape' => null,
        ];
        foreach ($expectedShadow as $key => $value) {
            self::assertEquals($value, $gridline->getShadowProperty($key), $key);
        }
    }
}
