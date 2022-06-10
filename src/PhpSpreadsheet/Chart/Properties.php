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
    const EXCEL_COLOR_TYPES = [
        self::EXCEL_COLOR_TYPE_ARGB,
        self::EXCEL_COLOR_TYPE_SCHEME,
        self::EXCEL_COLOR_TYPE_STANDARD,
    ];

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
    const FORMAT_CODE_DATE_ISO8601 = 'yyyy-mm-dd';
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

    const POINTS_WIDTH_MULTIPLIER = 12700;
    const ANGLE_MULTIPLIER = 60000; // direction and size-kx size-ky
    const PERCENTAGE_MULTIPLIER = 100000; // size sx and sy

    /**
     * @param float $width
     *
     * @return float
     */
    protected function getExcelPointsWidth($width)
    {
        return $width * self::POINTS_WIDTH_MULTIPLIER;
    }

    public static function pointsToXml(float $width): string
    {
        return (string) (int) ($width * self::POINTS_WIDTH_MULTIPLIER);
    }

    public static function xmlToPoints(string $width): float
    {
        return ((float) $width) / self::POINTS_WIDTH_MULTIPLIER;
    }

    public static function angleToXml(float $angle): string
    {
        return (string) (int) ($angle * self::ANGLE_MULTIPLIER);
    }

    public static function xmlToAngle(string $angle): float
    {
        return ((float) $angle) / self::ANGLE_MULTIPLIER;
    }

    public static function tenthOfPercentToXml(float $value): string
    {
        return (string) (int) ($value * self::PERCENTAGE_MULTIPLIER);
    }

    public static function xmlToTenthOfPercent(string $value): float
    {
        return ((float) $value) / self::PERCENTAGE_MULTIPLIER;
    }

    public static function alphaToXml(int $alpha): string
    {
        return (string) (100 - $alpha) . '000';
    }

    /**
     * @param float|int|string $alpha
     */
    public static function alphaFromXml($alpha): int
    {
        return 100 - ((int) $alpha / 1000);
    }

    /**
     * @param null|float|int|string $alpha
     */
    protected function setColorProperties(?string $color, $alpha, ?string $colorType): array
    {
        return [
            'type' => $colorType,
            'value' => $color,
            'alpha' => (int) $alpha,
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

    protected const PRESETS_OPTIONS = [
        //NONE
        0 => [
            'presets' => self::SHADOW_PRESETS_NOSHADOW,
            'effect' => null,
            'color' => [
                'type' => self::EXCEL_COLOR_TYPE_STANDARD,
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
        ],
        //OUTER
        1 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 2700000 / self::ANGLE_MULTIPLIER,
            'algn' => 'tl',
            'rotWithShape' => '0',
        ],
        2 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 5400000 / self::ANGLE_MULTIPLIER,
            'algn' => 't',
            'rotWithShape' => '0',
        ],
        3 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 8100000 / self::ANGLE_MULTIPLIER,
            'algn' => 'tr',
            'rotWithShape' => '0',
        ],
        4 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'algn' => 'l',
            'rotWithShape' => '0',
        ],
        5 => [
            'effect' => 'outerShdw',
            'size' => [
                'sx' => 102000 / self::PERCENTAGE_MULTIPLIER,
                'sy' => 102000 / self::PERCENTAGE_MULTIPLIER,
            ],
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'algn' => 'ctr',
            'rotWithShape' => '0',
        ],
        6 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 10800000 / self::ANGLE_MULTIPLIER,
            'algn' => 'r',
            'rotWithShape' => '0',
        ],
        7 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 18900000 / self::ANGLE_MULTIPLIER,
            'algn' => 'bl',
            'rotWithShape' => '0',
        ],
        8 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 16200000 / self::ANGLE_MULTIPLIER,
            'rotWithShape' => '0',
        ],
        9 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 13500000 / self::ANGLE_MULTIPLIER,
            'algn' => 'br',
            'rotWithShape' => '0',
        ],
        //INNER
        10 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 2700000 / self::ANGLE_MULTIPLIER,
        ],
        11 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 5400000 / self::ANGLE_MULTIPLIER,
        ],
        12 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 8100000 / self::ANGLE_MULTIPLIER,
        ],
        13 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
        ],
        14 => [
            'effect' => 'innerShdw',
            'blur' => 114300 / self::POINTS_WIDTH_MULTIPLIER,
        ],
        15 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 10800000 / self::ANGLE_MULTIPLIER,
        ],
        16 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 18900000 / self::ANGLE_MULTIPLIER,
        ],
        17 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 16200000 / self::ANGLE_MULTIPLIER,
        ],
        18 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 13500000 / self::ANGLE_MULTIPLIER,
        ],
        //perspective
        19 => [
            'effect' => 'outerShdw',
            'blur' => 152400 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 317500 / self::POINTS_WIDTH_MULTIPLIER,
            'size' => [
                'sx' => 90000 / self::PERCENTAGE_MULTIPLIER,
                'sy' => -19000 / self::PERCENTAGE_MULTIPLIER,
            ],
            'direction' => 5400000 / self::ANGLE_MULTIPLIER,
            'rotWithShape' => '0',
        ],
        20 => [
            'effect' => 'outerShdw',
            'blur' => 76200 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 18900000 / self::ANGLE_MULTIPLIER,
            'size' => [
                'sy' => 23000 / self::PERCENTAGE_MULTIPLIER,
                'kx' => -1200000 / self::ANGLE_MULTIPLIER,
            ],
            'algn' => 'bl',
            'rotWithShape' => '0',
        ],
        21 => [
            'effect' => 'outerShdw',
            'blur' => 76200 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 13500000 / self::ANGLE_MULTIPLIER,
            'size' => [
                'sy' => 23000 / self::PERCENTAGE_MULTIPLIER,
                'kx' => 1200000 / self::ANGLE_MULTIPLIER,
            ],
            'algn' => 'br',
            'rotWithShape' => '0',
        ],
        22 => [
            'effect' => 'outerShdw',
            'blur' => 76200 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 12700 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 2700000 / self::ANGLE_MULTIPLIER,
            'size' => [
                'sy' => -23000 / self::PERCENTAGE_MULTIPLIER,
                'kx' => -800400 / self::ANGLE_MULTIPLIER,
            ],
            'algn' => 'bl',
            'rotWithShape' => '0',
        ],
        23 => [
            'effect' => 'outerShdw',
            'blur' => 76200 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 12700 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 8100000 / self::ANGLE_MULTIPLIER,
            'size' => [
                'sy' => -23000 / self::PERCENTAGE_MULTIPLIER,
                'kx' => 800400 / self::ANGLE_MULTIPLIER,
            ],
            'algn' => 'br',
            'rotWithShape' => '0',
        ],
    ];

    protected function getShadowPresetsMap($presetsOption)
    {
        return self::PRESETS_OPTIONS[$presetsOption] ?? self::PRESETS_OPTIONS[0];
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
