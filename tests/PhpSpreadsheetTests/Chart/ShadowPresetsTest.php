<?php

namespace PhpOffice\PhpSpreadsheetTests\Chart;

use PhpOffice\PhpSpreadsheet\Chart\Axis;
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

    public function testOutOfRangePresets(): void
    {
        $axis = new Axis();
        $axis->setShadowProperties(99);
        $expectedShadow = [
            'presets' => Properties::SHADOW_PRESETS_NOSHADOW,
            'effect' => null,
            'color' => [
                'type' => Properties::EXCEL_COLOR_TYPE_STANDARD,
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
            self::assertEquals($axis->getShadowProperty($key), $value, $key);
        }
    }
}
