<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

/**
 * Created by PhpStorm.
 * User: nhw2h8s
 * Date: 7/2/14
 * Time: 5:45 PM.
 */
abstract class Properties
{
    const
        EXCEL_COLOR_TYPE_STANDARD = 'prstClr';
    const EXCEL_COLOR_TYPE_SCHEME = 'schemeClr';
    const EXCEL_COLOR_TYPE_ARGB = 'srgbClr';

    const
        AXIS_LABELS_LOW = 'low';
    const AXIS_LABELS_HIGH = 'high';
    const AXIS_LABELS_NEXT_TO = 'nextTo';
    const AXIS_LABELS_NONE = 'none';

    const
        TICK_MARK_NONE = 'none';
    const TICK_MARK_INSIDE = 'in';
    const TICK_MARK_OUTSIDE = 'out';
    const TICK_MARK_CROSS = 'cross';

    const
        HORIZONTAL_CROSSES_AUTOZERO = 'autoZero';
    const HORIZONTAL_CROSSES_MAXIMUM = 'max';

    const
        FORMAT_CODE_GENERAL = 'General';
    const FORMAT_CODE_NUMBER = '#,##0.00';
    const FORMAT_CODE_CURRENCY = '$#,##0.00';
    const FORMAT_CODE_ACCOUNTING = '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)';
    const FORMAT_CODE_DATE = 'm/d/yyyy';
    const FORMAT_CODE_TIME = '[$-F400]h:mm:ss AM/PM';
    const FORMAT_CODE_PERCENTAGE = '0.00%';
    const FORMAT_CODE_FRACTION = '# ?/?';
    const FORMAT_CODE_SCIENTIFIC = '0.00E+00';
    const FORMAT_CODE_TEXT = '@';
    const FORMAT_CODE_SPECIAL = '00000';

    const
        ORIENTATION_NORMAL = 'minMax';
    const ORIENTATION_REVERSED = 'maxMin';

    const
        LINE_STYLE_COMPOUND_SIMPLE = 'sng';
    const LINE_STYLE_COMPOUND_DOUBLE = 'dbl';
    const LINE_STYLE_COMPOUND_THICKTHIN = 'thickThin';
    const LINE_STYLE_COMPOUND_THINTHICK = 'thinThick';
    const LINE_STYLE_COMPOUND_TRIPLE = 'tri';
    const LINE_STYLE_DASH_SOLID = 'solid';
    const LINE_STYLE_DASH_ROUND_DOT = 'sysDot';
    const LINE_STYLE_DASH_SQUERE_DOT = 'sysDash';
    const LINE_STYPE_DASH_DASH = 'dash';
    const LINE_STYLE_DASH_DASH_DOT = 'dashDot';
    const LINE_STYLE_DASH_LONG_DASH = 'lgDash';
    const LINE_STYLE_DASH_LONG_DASH_DOT = 'lgDashDot';
    const LINE_STYLE_DASH_LONG_DASH_DOT_DOT = 'lgDashDotDot';
    const LINE_STYLE_CAP_SQUARE = 'sq';
    const LINE_STYLE_CAP_ROUND = 'rnd';
    const LINE_STYLE_CAP_FLAT = 'flat';
    const LINE_STYLE_JOIN_ROUND = 'bevel';
    const LINE_STYLE_JOIN_MITER = 'miter';
    const LINE_STYLE_JOIN_BEVEL = 'bevel';
    const LINE_STYLE_ARROW_TYPE_NOARROW = null;
    const LINE_STYLE_ARROW_TYPE_ARROW = 'triangle';
    const LINE_STYLE_ARROW_TYPE_OPEN = 'arrow';
    const LINE_STYLE_ARROW_TYPE_STEALTH = 'stealth';
    const LINE_STYLE_ARROW_TYPE_DIAMOND = 'diamond';
    const LINE_STYLE_ARROW_TYPE_OVAL = 'oval';
    const LINE_STYLE_ARROW_SIZE_1 = 1;
    const LINE_STYLE_ARROW_SIZE_2 = 2;
    const LINE_STYLE_ARROW_SIZE_3 = 3;
    const LINE_STYLE_ARROW_SIZE_4 = 4;
    const LINE_STYLE_ARROW_SIZE_5 = 5;
    const LINE_STYLE_ARROW_SIZE_6 = 6;
    const LINE_STYLE_ARROW_SIZE_7 = 7;
    const LINE_STYLE_ARROW_SIZE_8 = 8;
    const LINE_STYLE_ARROW_SIZE_9 = 9;

    const
        SHADOW_PRESETS_NOSHADOW = null;
    const SHADOW_PRESETS_OUTER_BOTTTOM_RIGHT = 1;
    const SHADOW_PRESETS_OUTER_BOTTOM = 2;
    const SHADOW_PRESETS_OUTER_BOTTOM_LEFT = 3;
    const SHADOW_PRESETS_OUTER_RIGHT = 4;
    const SHADOW_PRESETS_OUTER_CENTER = 5;
    const SHADOW_PRESETS_OUTER_LEFT = 6;
    const SHADOW_PRESETS_OUTER_TOP_RIGHT = 7;
    const SHADOW_PRESETS_OUTER_TOP = 8;
    const SHADOW_PRESETS_OUTER_TOP_LEFT = 9;
    const SHADOW_PRESETS_INNER_BOTTTOM_RIGHT = 10;
    const SHADOW_PRESETS_INNER_BOTTOM = 11;
    const SHADOW_PRESETS_INNER_BOTTOM_LEFT = 12;
    const SHADOW_PRESETS_INNER_RIGHT = 13;
    const SHADOW_PRESETS_INNER_CENTER = 14;
    const SHADOW_PRESETS_INNER_LEFT = 15;
    const SHADOW_PRESETS_INNER_TOP_RIGHT = 16;
    const SHADOW_PRESETS_INNER_TOP = 17;
    const SHADOW_PRESETS_INNER_TOP_LEFT = 18;
    const SHADOW_PRESETS_PERSPECTIVE_BELOW = 19;
    const SHADOW_PRESETS_PERSPECTIVE_UPPER_RIGHT = 20;
    const SHADOW_PRESETS_PERSPECTIVE_UPPER_LEFT = 21;
    const SHADOW_PRESETS_PERSPECTIVE_LOWER_RIGHT = 22;
    const SHADOW_PRESETS_PERSPECTIVE_LOWER_LEFT = 23;

    /**
     * @param float $width
     *
     * @return float
     */
    protected function getExcelPointsWidth($width)
    {
        return $width * 12700;
    }

    /**
     * @param float $angle
     *
     * @return float
     */
    protected function getExcelPointsAngle($angle)
    {
        return $angle * 60000;
    }

    protected function getTrueAlpha($alpha)
    {
        return (string) 100 - $alpha . '000';
    }

    protected function setColorProperties($color, $alpha, $colorType)
    {
        return [
            'type' => (string) $colorType,
            'value' => (string) $color,
            'alpha' => (string) $this->getTrueAlpha($alpha),
        ];
    }

    protected function getLineStyleArrowSize($arraySelector, $arrayKaySelector)
    {
        $sizes = [
            1 => ['w' => 'sm', 'len' => 'sm'],
            2 => ['w' => 'sm', 'len' => 'med'],
            3 => ['w' => 'sm', 'len' => 'lg'],
            4 => ['w' => 'med', 'len' => 'sm'],
            5 => ['w' => 'med', 'len' => 'med'],
            6 => ['w' => 'med', 'len' => 'lg'],
            7 => ['w' => 'lg', 'len' => 'sm'],
            8 => ['w' => 'lg', 'len' => 'med'],
            9 => ['w' => 'lg', 'len' => 'lg'],
        ];

        return $sizes[$arraySelector][$arrayKaySelector];
    }

    protected function getShadowPresetsMap($presetsOption)
    {
        $presets_options = [
            //OUTER
            1 => [
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '2700000',
                'algn' => 'tl',
                'rotWithShape' => '0',
            ],
            2 => [
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '5400000',
                'algn' => 't',
                'rotWithShape' => '0',
            ],
            3 => [
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '8100000',
                'algn' => 'tr',
                'rotWithShape' => '0',
            ],
            4 => [
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'algn' => 'l',
                'rotWithShape' => '0',
            ],
            5 => [
                'effect' => 'outerShdw',
                'size' => [
                    'sx' => '102000',
                    'sy' => '102000',
                ],
                'blur' => '63500',
                'distance' => '38100',
                'algn' => 'ctr',
                'rotWithShape' => '0',
            ],
            6 => [
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '10800000',
                'algn' => 'r',
                'rotWithShape' => '0',
            ],
            7 => [
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '18900000',
                'algn' => 'bl',
                'rotWithShape' => '0',
            ],
            8 => [
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '16200000',
                'rotWithShape' => '0',
            ],
            9 => [
                'effect' => 'outerShdw',
                'blur' => '50800',
                'distance' => '38100',
                'direction' => '13500000',
                'algn' => 'br',
                'rotWithShape' => '0',
            ],
            //INNER
            10 => [
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '2700000',
            ],
            11 => [
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '5400000',
            ],
            12 => [
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '8100000',
            ],
            13 => [
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
            ],
            14 => [
                'effect' => 'innerShdw',
                'blur' => '114300',
            ],
            15 => [
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '10800000',
            ],
            16 => [
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '18900000',
            ],
            17 => [
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '16200000',
            ],
            18 => [
                'effect' => 'innerShdw',
                'blur' => '63500',
                'distance' => '50800',
                'direction' => '13500000',
            ],
            //perspective
            19 => [
                'effect' => 'outerShdw',
                'blur' => '152400',
                'distance' => '317500',
                'size' => [
                    'sx' => '90000',
                    'sy' => '-19000',
                ],
                'direction' => '5400000',
                'rotWithShape' => '0',
            ],
            20 => [
                'effect' => 'outerShdw',
                'blur' => '76200',
                'direction' => '18900000',
                'size' => [
                    'sy' => '23000',
                    'kx' => '-1200000',
                ],
                'algn' => 'bl',
                'rotWithShape' => '0',
            ],
            21 => [
                'effect' => 'outerShdw',
                'blur' => '76200',
                'direction' => '13500000',
                'size' => [
                    'sy' => '23000',
                    'kx' => '1200000',
                ],
                'algn' => 'br',
                'rotWithShape' => '0',
            ],
            22 => [
                'effect' => 'outerShdw',
                'blur' => '76200',
                'distance' => '12700',
                'direction' => '2700000',
                'size' => [
                    'sy' => '-23000',
                    'kx' => '-800400',
                ],
                'algn' => 'bl',
                'rotWithShape' => '0',
            ],
            23 => [
                'effect' => 'outerShdw',
                'blur' => '76200',
                'distance' => '12700',
                'direction' => '8100000',
                'size' => [
                    'sy' => '-23000',
                    'kx' => '800400',
                ],
                'algn' => 'br',
                'rotWithShape' => '0',
            ],
        ];

        return $presets_options[$presetsOption];
    }

    protected function getArrayElementsValue($properties, $elements)
    {
        $reference = &$properties;
        if (!is_array($elements)) {
            return $reference[$elements];
        }

        foreach ($elements as $keys) {
            $reference = &$reference[$keys];
        }

        return $reference;
    }
}
