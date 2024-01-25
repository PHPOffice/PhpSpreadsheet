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
    public const AXIS_LABELS_LOW = 'low';
    public const AXIS_LABELS_HIGH = 'high';
    public const AXIS_LABELS_NEXT_TO = 'nextTo';
    public const AXIS_LABELS_NONE = 'none';

    public const TICK_MARK_NONE = 'none';
    public const TICK_MARK_INSIDE = 'in';
    public const TICK_MARK_OUTSIDE = 'out';
    public const TICK_MARK_CROSS = 'cross';

    public const HORIZONTAL_CROSSES_AUTOZERO = 'autoZero';
    public const HORIZONTAL_CROSSES_MAXIMUM = 'max';

    public const FORMAT_CODE_GENERAL = 'General';
    public const FORMAT_CODE_NUMBER = '#,##0.00';
    public const FORMAT_CODE_CURRENCY = '$#,##0.00';
    public const FORMAT_CODE_ACCOUNTING = '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)';
    public const FORMAT_CODE_DATE = 'm/d/yyyy';
    public const FORMAT_CODE_DATE_ISO8601 = 'yyyy-mm-dd';
    public const FORMAT_CODE_TIME = '[$-F400]h:mm:ss AM/PM';
    public const FORMAT_CODE_PERCENTAGE = '0.00%';
    public const FORMAT_CODE_FRACTION = '# ?/?';
    public const FORMAT_CODE_SCIENTIFIC = '0.00E+00';
    public const FORMAT_CODE_TEXT = '@';
    public const FORMAT_CODE_SPECIAL = '00000';

    public const ORIENTATION_NORMAL = 'minMax';
    public const ORIENTATION_REVERSED = 'maxMin';

    public const LINE_STYLE_COMPOUND_SIMPLE = 'sng';
    public const LINE_STYLE_COMPOUND_DOUBLE = 'dbl';
    public const LINE_STYLE_COMPOUND_THICKTHIN = 'thickThin';
    public const LINE_STYLE_COMPOUND_THINTHICK = 'thinThick';
    public const LINE_STYLE_COMPOUND_TRIPLE = 'tri';
    public const LINE_STYLE_DASH_SOLID = 'solid';
    public const LINE_STYLE_DASH_ROUND_DOT = 'sysDot';
    public const LINE_STYLE_DASH_SQUARE_DOT = 'sysDash';
    public const LINE_STYPE_DASH_DASH = 'dash';
    public const LINE_STYLE_DASH_DASH_DOT = 'dashDot';
    public const LINE_STYLE_DASH_LONG_DASH = 'lgDash';
    public const LINE_STYLE_DASH_LONG_DASH_DOT = 'lgDashDot';
    public const LINE_STYLE_DASH_LONG_DASH_DOT_DOT = 'lgDashDotDot';
    public const LINE_STYLE_CAP_SQUARE = 'sq';
    public const LINE_STYLE_CAP_ROUND = 'rnd';
    public const LINE_STYLE_CAP_FLAT = 'flat';
    public const LINE_STYLE_JOIN_ROUND = 'round';
    public const LINE_STYLE_JOIN_MITER = 'miter';
    public const LINE_STYLE_JOIN_BEVEL = 'bevel';
    public const LINE_STYLE_ARROW_TYPE_NOARROW = null;
    public const LINE_STYLE_ARROW_TYPE_ARROW = 'triangle';
    public const LINE_STYLE_ARROW_TYPE_OPEN = 'arrow';
    public const LINE_STYLE_ARROW_TYPE_STEALTH = 'stealth';
    public const LINE_STYLE_ARROW_TYPE_DIAMOND = 'diamond';
    public const LINE_STYLE_ARROW_TYPE_OVAL = 'oval';
    public const LINE_STYLE_ARROW_SIZE_1 = 1;
    public const LINE_STYLE_ARROW_SIZE_2 = 2;
    public const LINE_STYLE_ARROW_SIZE_3 = 3;
    public const LINE_STYLE_ARROW_SIZE_4 = 4;
    public const LINE_STYLE_ARROW_SIZE_5 = 5;
    public const LINE_STYLE_ARROW_SIZE_6 = 6;
    public const LINE_STYLE_ARROW_SIZE_7 = 7;
    public const LINE_STYLE_ARROW_SIZE_8 = 8;
    public const LINE_STYLE_ARROW_SIZE_9 = 9;

    public const SHADOW_PRESETS_NOSHADOW = null;
    public const SHADOW_PRESETS_OUTER_BOTTTOM_RIGHT = 1;
    public const SHADOW_PRESETS_OUTER_BOTTOM = 2;
    public const SHADOW_PRESETS_OUTER_BOTTOM_LEFT = 3;
    public const SHADOW_PRESETS_OUTER_RIGHT = 4;
    public const SHADOW_PRESETS_OUTER_CENTER = 5;
    public const SHADOW_PRESETS_OUTER_LEFT = 6;
    public const SHADOW_PRESETS_OUTER_TOP_RIGHT = 7;
    public const SHADOW_PRESETS_OUTER_TOP = 8;
    public const SHADOW_PRESETS_OUTER_TOP_LEFT = 9;
    public const SHADOW_PRESETS_INNER_BOTTTOM_RIGHT = 10;
    public const SHADOW_PRESETS_INNER_BOTTOM = 11;
    public const SHADOW_PRESETS_INNER_BOTTOM_LEFT = 12;
    public const SHADOW_PRESETS_INNER_RIGHT = 13;
    public const SHADOW_PRESETS_INNER_CENTER = 14;
    public const SHADOW_PRESETS_INNER_LEFT = 15;
    public const SHADOW_PRESETS_INNER_TOP_RIGHT = 16;
    public const SHADOW_PRESETS_INNER_TOP = 17;
    public const SHADOW_PRESETS_INNER_TOP_LEFT = 18;
    public const SHADOW_PRESETS_PERSPECTIVE_BELOW = 19;
    public const SHADOW_PRESETS_PERSPECTIVE_UPPER_RIGHT = 20;
    public const SHADOW_PRESETS_PERSPECTIVE_UPPER_LEFT = 21;
    public const SHADOW_PRESETS_PERSPECTIVE_LOWER_RIGHT = 22;
    public const SHADOW_PRESETS_PERSPECTIVE_LOWER_LEFT = 23;

    public const POINTS_WIDTH_MULTIPLIER = 12700;
    public const ANGLE_MULTIPLIER = 60000; // direction and size-kx size-ky
    public const PERCENTAGE_MULTIPLIER = 100000; // size sx and sy

    protected bool $objectState = false; // used only for minor gridlines

    /** @var ?float */
    protected ?float $glowSize = null;

    protected ChartColor $glowColor;

    protected array $softEdges = [
        'size' => null,
    ];

    protected array $shadowProperties = self::PRESETS_OPTIONS[0];

    protected ChartColor $shadowColor;

    public function __construct()
    {
        $this->lineColor = new ChartColor();
        $this->glowColor = new ChartColor();
        $this->shadowColor = new ChartColor();
        $this->shadowColor->setType(ChartColor::EXCEL_COLOR_TYPE_STANDARD);
        $this->shadowColor->setValue('black');
        $this->shadowColor->setAlpha(40);
    }

    /**
     * Get Object State.
     */
    public function getObjectState(): bool
    {
        return $this->objectState;
    }

    /**
     * Change Object State to True.
     *
     * @return $this
     */
    public function activateObject()
    {
        $this->objectState = true;

        return $this;
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

    protected function setColorProperties(?string $color, null|float|int|string $alpha, ?string $colorType): array
    {
        return [
            'type' => $colorType,
            'value' => $color,
            'alpha' => ($alpha === null) ? null : (int) $alpha,
        ];
    }

    protected const PRESETS_OPTIONS = [
        //NONE
        0 => [
            'presets' => self::SHADOW_PRESETS_NOSHADOW,
            'effect' => null,
            //'color' => [
            //    'type' => ChartColor::EXCEL_COLOR_TYPE_STANDARD,
            //    'value' => 'black',
            //    'alpha' => 40,
            //],
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
            'direction' => 2_700_000 / self::ANGLE_MULTIPLIER,
            'algn' => 'tl',
            'rotWithShape' => '0',
        ],
        2 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 5_400_000 / self::ANGLE_MULTIPLIER,
            'algn' => 't',
            'rotWithShape' => '0',
        ],
        3 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 8_100_000 / self::ANGLE_MULTIPLIER,
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
            'direction' => 10_800_000 / self::ANGLE_MULTIPLIER,
            'algn' => 'r',
            'rotWithShape' => '0',
        ],
        7 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 18_900_000 / self::ANGLE_MULTIPLIER,
            'algn' => 'bl',
            'rotWithShape' => '0',
        ],
        8 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 16_200_000 / self::ANGLE_MULTIPLIER,
            'rotWithShape' => '0',
        ],
        9 => [
            'effect' => 'outerShdw',
            'blur' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 38100 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 13_500_000 / self::ANGLE_MULTIPLIER,
            'algn' => 'br',
            'rotWithShape' => '0',
        ],
        //INNER
        10 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 2_700_000 / self::ANGLE_MULTIPLIER,
        ],
        11 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 5_400_000 / self::ANGLE_MULTIPLIER,
        ],
        12 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 8_100_000 / self::ANGLE_MULTIPLIER,
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
            'direction' => 10_800_000 / self::ANGLE_MULTIPLIER,
        ],
        16 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 18_900_000 / self::ANGLE_MULTIPLIER,
        ],
        17 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 16_200_000 / self::ANGLE_MULTIPLIER,
        ],
        18 => [
            'effect' => 'innerShdw',
            'blur' => 63500 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 50800 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 13_500_000 / self::ANGLE_MULTIPLIER,
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
            'direction' => 5_400_000 / self::ANGLE_MULTIPLIER,
            'rotWithShape' => '0',
        ],
        20 => [
            'effect' => 'outerShdw',
            'blur' => 76200 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 18_900_000 / self::ANGLE_MULTIPLIER,
            'size' => [
                'sy' => 23000 / self::PERCENTAGE_MULTIPLIER,
                'kx' => -1_200_000 / self::ANGLE_MULTIPLIER,
            ],
            'algn' => 'bl',
            'rotWithShape' => '0',
        ],
        21 => [
            'effect' => 'outerShdw',
            'blur' => 76200 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 13_500_000 / self::ANGLE_MULTIPLIER,
            'size' => [
                'sy' => 23000 / self::PERCENTAGE_MULTIPLIER,
                'kx' => 1_200_000 / self::ANGLE_MULTIPLIER,
            ],
            'algn' => 'br',
            'rotWithShape' => '0',
        ],
        22 => [
            'effect' => 'outerShdw',
            'blur' => 76200 / self::POINTS_WIDTH_MULTIPLIER,
            'distance' => 12700 / self::POINTS_WIDTH_MULTIPLIER,
            'direction' => 2_700_000 / self::ANGLE_MULTIPLIER,
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
            'direction' => 8_100_000 / self::ANGLE_MULTIPLIER,
            'size' => [
                'sy' => -23000 / self::PERCENTAGE_MULTIPLIER,
                'kx' => 800400 / self::ANGLE_MULTIPLIER,
            ],
            'algn' => 'br',
            'rotWithShape' => '0',
        ],
    ];

    protected function getShadowPresetsMap(int $presetsOption): array
    {
        return self::PRESETS_OPTIONS[$presetsOption] ?? self::PRESETS_OPTIONS[0];
    }

    /**
     * Get value of array element.
     */
    protected function getArrayElementsValue(array $properties, array|int|string $elements): mixed
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

    /**
     * Set Glow Properties.
     */
    public function setGlowProperties(float $size, ?string $colorValue = null, ?int $colorAlpha = null, ?string $colorType = null): void
    {
        $this
            ->activateObject()
            ->setGlowSize($size);
        $this->glowColor->setColorPropertiesArray(
            [
                'value' => $colorValue,
                'type' => $colorType,
                'alpha' => $colorAlpha,
            ]
        );
    }

    /**
     * Get Glow Property.
     */
    public function getGlowProperty(array|string $property): null|array|float|int|string
    {
        $retVal = null;
        if ($property === 'size') {
            $retVal = $this->glowSize;
        } elseif ($property === 'color') {
            $retVal = [
                'value' => $this->glowColor->getColorProperty('value'),
                'type' => $this->glowColor->getColorProperty('type'),
                'alpha' => $this->glowColor->getColorProperty('alpha'),
            ];
        } elseif (is_array($property) && count($property) >= 2 && $property[0] === 'color') {
            $retVal = $this->glowColor->getColorProperty($property[1]);
        }

        return $retVal;
    }

    /**
     * Get Glow Color Property.
     */
    public function getGlowColor(string $propertyName): null|int|string
    {
        return $this->glowColor->getColorProperty($propertyName);
    }

    public function getGlowColorObject(): ChartColor
    {
        return $this->glowColor;
    }

    /**
     * Get Glow Size.
     */
    public function getGlowSize(): ?float
    {
        return $this->glowSize;
    }

    /**
     * Set Glow Size.
     *
     * @return $this
     */
    protected function setGlowSize(?float $size)
    {
        $this->glowSize = $size;

        return $this;
    }

    /**
     * Set Soft Edges Size.
     */
    public function setSoftEdges(?float $size): void
    {
        if ($size !== null) {
            $this->activateObject();
            $this->softEdges['size'] = $size;
        }
    }

    /**
     * Get Soft Edges Size.
     */
    public function getSoftEdgesSize(): ?float
    {
        return $this->softEdges['size'];
    }

    public function setShadowProperty(string $propertyName, mixed $value): self
    {
        $this->activateObject();
        if ($propertyName === 'color' && is_array($value)) {
            $this->shadowColor->setColorPropertiesArray($value);
        } else {
            $this->shadowProperties[$propertyName] = $value;
        }

        return $this;
    }

    /**
     * Set Shadow Properties.
     */
    public function setShadowProperties(int $presets, ?string $colorValue = null, ?string $colorType = null, null|float|int|string $colorAlpha = null, ?float $blur = null, ?int $angle = null, ?float $distance = null): void
    {
        $this->activateObject()->setShadowPresetsProperties((int) $presets);
        if ($presets === 0) {
            $this->shadowColor->setType(ChartColor::EXCEL_COLOR_TYPE_STANDARD);
            $this->shadowColor->setValue('black');
            $this->shadowColor->setAlpha(40);
        }
        if ($colorValue !== null) {
            $this->shadowColor->setValue($colorValue);
        }
        if ($colorType !== null) {
            $this->shadowColor->setType($colorType);
        }
        if (is_numeric($colorAlpha)) {
            $this->shadowColor->setAlpha((int) $colorAlpha);
        }
        $this
            ->setShadowBlur($blur)
            ->setShadowAngle($angle)
            ->setShadowDistance($distance);
    }

    /**
     * Set Shadow Presets Properties.
     *
     * @return $this
     */
    protected function setShadowPresetsProperties(int $presets)
    {
        $this->shadowProperties['presets'] = $presets;
        $this->setShadowPropertiesMapValues($this->getShadowPresetsMap($presets));

        return $this;
    }

    protected const SHADOW_ARRAY_KEYS = ['size', 'color'];

    /**
     * Set Shadow Properties Values.
     *
     * @return $this
     */
    protected function setShadowPropertiesMapValues(array $propertiesMap, ?array &$reference = null)
    {
        $base_reference = $reference;
        foreach ($propertiesMap as $property_key => $property_val) {
            if (is_array($property_val)) {
                if (in_array($property_key, self::SHADOW_ARRAY_KEYS, true)) {
                    $reference = &$this->shadowProperties[$property_key];
                    $this->setShadowPropertiesMapValues($property_val, $reference);
                }
            } else {
                if ($base_reference === null) {
                    $this->shadowProperties[$property_key] = $property_val;
                } else {
                    $reference[$property_key] = $property_val;
                }
            }
        }

        return $this;
    }

    /**
     * Set Shadow Blur.
     *
     * @return $this
     */
    protected function setShadowBlur(?float $blur)
    {
        if ($blur !== null) {
            $this->shadowProperties['blur'] = $blur;
        }

        return $this;
    }

    /**
     * Set Shadow Angle.
     *
     * @return $this
     */
    protected function setShadowAngle(null|float|int|string $angle)
    {
        if (is_numeric($angle)) {
            $this->shadowProperties['direction'] = $angle;
        }

        return $this;
    }

    /**
     * Set Shadow Distance.
     *
     * @return $this
     */
    protected function setShadowDistance(?float $distance)
    {
        if ($distance !== null) {
            $this->shadowProperties['distance'] = $distance;
        }

        return $this;
    }

    public function getShadowColorObject(): ChartColor
    {
        return $this->shadowColor;
    }

    /**
     * Get Shadow Property.
     *
     * @param string|string[] $elements
     */
    public function getShadowProperty($elements): array|string|null
    {
        if ($elements === 'color') {
            return [
                'value' => $this->shadowColor->getValue(),
                'type' => $this->shadowColor->getType(),
                'alpha' => $this->shadowColor->getAlpha(),
            ];
        }

        return $this->getArrayElementsValue($this->shadowProperties, $elements);
    }

    public function getShadowArray(): array
    {
        $array = $this->shadowProperties;
        if ($this->getShadowColorObject()->isUsable()) {
            $array['color'] = $this->getShadowProperty('color');
        }

        return $array;
    }

    protected ChartColor $lineColor;

    protected array $lineStyleProperties = [
        'width' => null, //'9525',
        'compound' => '', //self::LINE_STYLE_COMPOUND_SIMPLE,
        'dash' => '', //self::LINE_STYLE_DASH_SOLID,
        'cap' => '', //self::LINE_STYLE_CAP_FLAT,
        'join' => '', //self::LINE_STYLE_JOIN_BEVEL,
        'arrow' => [
            'head' => [
                'type' => '', //self::LINE_STYLE_ARROW_TYPE_NOARROW,
                'size' => '', //self::LINE_STYLE_ARROW_SIZE_5,
                'w' => '',
                'len' => '',
            ],
            'end' => [
                'type' => '', //self::LINE_STYLE_ARROW_TYPE_NOARROW,
                'size' => '', //self::LINE_STYLE_ARROW_SIZE_8,
                'w' => '',
                'len' => '',
            ],
        ],
    ];

    public function copyLineStyles(self $otherProperties): void
    {
        $this->lineStyleProperties = $otherProperties->lineStyleProperties;
        $this->lineColor = $otherProperties->lineColor;
        $this->glowSize = $otherProperties->glowSize;
        $this->glowColor = $otherProperties->glowColor;
        $this->softEdges = $otherProperties->softEdges;
        $this->shadowProperties = $otherProperties->shadowProperties;
    }

    public function getLineColor(): ChartColor
    {
        return $this->lineColor;
    }

    /**
     * Set Line Color Properties.
     */
    public function setLineColorProperties(?string $value, ?int $alpha = null, ?string $colorType = null): void
    {
        $this->activateObject();
        $this->lineColor->setColorPropertiesArray(
            $this->setColorProperties(
                $value,
                $alpha,
                $colorType
            )
        );
    }

    /**
     * Get Line Color Property.
     */
    public function getLineColorProperty(string $propertyName): null|int|string
    {
        return $this->lineColor->getColorProperty($propertyName);
    }

    /**
     * Set Line Style Properties.
     */
    public function setLineStyleProperties(
        null|float|int|string $lineWidth = null,
        ?string $compoundType = '',
        ?string $dashType = '',
        ?string $capType = '',
        ?string $joinType = '',
        ?string $headArrowType = '',
        int $headArrowSize = 0,
        ?string $endArrowType = '',
        int $endArrowSize = 0,
        ?string $headArrowWidth = '',
        ?string $headArrowLength = '',
        ?string $endArrowWidth = '',
        ?string $endArrowLength = ''
    ): void {
        $this->activateObject();
        if (is_numeric($lineWidth)) {
            $this->lineStyleProperties['width'] = $lineWidth;
        }
        if ($compoundType !== '') {
            $this->lineStyleProperties['compound'] = $compoundType;
        }
        if ($dashType !== '') {
            $this->lineStyleProperties['dash'] = $dashType;
        }
        if ($capType !== '') {
            $this->lineStyleProperties['cap'] = $capType;
        }
        if ($joinType !== '') {
            $this->lineStyleProperties['join'] = $joinType;
        }
        if ($headArrowType !== '') {
            $this->lineStyleProperties['arrow']['head']['type'] = $headArrowType;
        }
        if (isset(self::ARROW_SIZES[$headArrowSize])) {
            $this->lineStyleProperties['arrow']['head']['size'] = $headArrowSize;
            $this->lineStyleProperties['arrow']['head']['w'] = self::ARROW_SIZES[$headArrowSize]['w'];
            $this->lineStyleProperties['arrow']['head']['len'] = self::ARROW_SIZES[$headArrowSize]['len'];
        }
        if ($endArrowType !== '') {
            $this->lineStyleProperties['arrow']['end']['type'] = $endArrowType;
        }
        if (isset(self::ARROW_SIZES[$endArrowSize])) {
            $this->lineStyleProperties['arrow']['end']['size'] = $endArrowSize;
            $this->lineStyleProperties['arrow']['end']['w'] = self::ARROW_SIZES[$endArrowSize]['w'];
            $this->lineStyleProperties['arrow']['end']['len'] = self::ARROW_SIZES[$endArrowSize]['len'];
        }
        if ($headArrowWidth !== '') {
            $this->lineStyleProperties['arrow']['head']['w'] = $headArrowWidth;
        }
        if ($headArrowLength !== '') {
            $this->lineStyleProperties['arrow']['head']['len'] = $headArrowLength;
        }
        if ($endArrowWidth !== '') {
            $this->lineStyleProperties['arrow']['end']['w'] = $endArrowWidth;
        }
        if ($endArrowLength !== '') {
            $this->lineStyleProperties['arrow']['end']['len'] = $endArrowLength;
        }
    }

    public function getLineStyleArray(): array
    {
        return $this->lineStyleProperties;
    }

    public function setLineStyleArray(array $lineStyleProperties = []): self
    {
        $this->activateObject();
        $this->lineStyleProperties['width'] = $lineStyleProperties['width'] ?? null;
        $this->lineStyleProperties['compound'] = $lineStyleProperties['compound'] ?? '';
        $this->lineStyleProperties['dash'] = $lineStyleProperties['dash'] ?? '';
        $this->lineStyleProperties['cap'] = $lineStyleProperties['cap'] ?? '';
        $this->lineStyleProperties['join'] = $lineStyleProperties['join'] ?? '';
        $this->lineStyleProperties['arrow']['head']['type'] = $lineStyleProperties['arrow']['head']['type'] ?? '';
        $this->lineStyleProperties['arrow']['head']['size'] = $lineStyleProperties['arrow']['head']['size'] ?? '';
        $this->lineStyleProperties['arrow']['head']['w'] = $lineStyleProperties['arrow']['head']['w'] ?? '';
        $this->lineStyleProperties['arrow']['head']['len'] = $lineStyleProperties['arrow']['head']['len'] ?? '';
        $this->lineStyleProperties['arrow']['end']['type'] = $lineStyleProperties['arrow']['end']['type'] ?? '';
        $this->lineStyleProperties['arrow']['end']['size'] = $lineStyleProperties['arrow']['end']['size'] ?? '';
        $this->lineStyleProperties['arrow']['end']['w'] = $lineStyleProperties['arrow']['end']['w'] ?? '';
        $this->lineStyleProperties['arrow']['end']['len'] = $lineStyleProperties['arrow']['end']['len'] ?? '';

        return $this;
    }

    public function setLineStyleProperty(string $propertyName, mixed $value): self
    {
        $this->activateObject();
        $this->lineStyleProperties[$propertyName] = $value;

        return $this;
    }

    /**
     * Get Line Style Property.
     */
    public function getLineStyleProperty(array|string $elements): ?string
    {
        return $this->getArrayElementsValue($this->lineStyleProperties, $elements);
    }

    protected const ARROW_SIZES = [
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

    /**
     * Get Line Style Arrow Size.
     */
    protected function getLineStyleArrowSize(int $arraySelector, string $arrayKaySelector): string
    {
        return self::ARROW_SIZES[$arraySelector][$arrayKaySelector] ?? '';
    }

    /**
     * Get Line Style Arrow Parameters.
     */
    public function getLineStyleArrowParameters(string $arrowSelector, string $propertySelector): string
    {
        return $this->getLineStyleArrowSize($this->lineStyleProperties['arrow'][$arrowSelector]['size'], $propertySelector);
    }

    /**
     * Get Line Style Arrow Width.
     */
    public function getLineStyleArrowWidth(string $arrow): ?string
    {
        return $this->getLineStyleProperty(['arrow', $arrow, 'w']);
    }

    /**
     * Get Line Style Arrow Excel Length.
     */
    public function getLineStyleArrowLength(string $arrow): ?string
    {
        return $this->getLineStyleProperty(['arrow', $arrow, 'len']);
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $this->lineColor = clone $this->lineColor;
        $this->glowColor = clone $this->glowColor;
        $this->shadowColor = clone $this->shadowColor;
    }
}
