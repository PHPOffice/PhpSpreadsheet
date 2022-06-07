<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

/**
 * Created by PhpStorm.
 * User: Wiktor Trzonkowski
 * Date: 6/17/14
 * Time: 12:11 PM.
 */
class Axis extends Properties
{
    /**
     * Axis Number.
     *
     * @var mixed[]
     */
    private $axisNumber = [
        'format' => self::FORMAT_CODE_GENERAL,
        'source_linked' => 1,
        'numeric' => null,
    ];

    /**
     * Axis Options.
     *
     * @var mixed[]
     */
    private $axisOptions = [
        'minimum' => null,
        'maximum' => null,
        'major_unit' => null,
        'minor_unit' => null,
        'orientation' => self::ORIENTATION_NORMAL,
        'minor_tick_mark' => self::TICK_MARK_NONE,
        'major_tick_mark' => self::TICK_MARK_NONE,
        'axis_labels' => self::AXIS_LABELS_NEXT_TO,
        'horizontal_crosses' => self::HORIZONTAL_CROSSES_AUTOZERO,
        'horizontal_crosses_value' => null,
    ];

    /**
     * Fill Properties.
     *
     * @var mixed[]
     */
    private $fillProperties = [
        'type' => self::EXCEL_COLOR_TYPE_ARGB,
        'value' => null,
        'alpha' => 0,
    ];

    /**
     * Line Properties.
     *
     * @var mixed[]
     */
    private $lineProperties = [
        'type' => self::EXCEL_COLOR_TYPE_ARGB,
        'value' => null,
        'alpha' => 0,
    ];

    /**
     * Line Style Properties.
     *
     * @var mixed[]
     */
    private $lineStyleProperties = [
        'width' => '9525',
        'compound' => self::LINE_STYLE_COMPOUND_SIMPLE,
        'dash' => self::LINE_STYLE_DASH_SOLID,
        'cap' => self::LINE_STYLE_CAP_FLAT,
        'join' => self::LINE_STYLE_JOIN_BEVEL,
        'arrow' => [
            'head' => [
                'type' => self::LINE_STYLE_ARROW_TYPE_NOARROW,
                'size' => self::LINE_STYLE_ARROW_SIZE_5,
            ],
            'end' => [
                'type' => self::LINE_STYLE_ARROW_TYPE_NOARROW,
                'size' => self::LINE_STYLE_ARROW_SIZE_8,
            ],
        ],
    ];

    /**
     * Shadow Properties.
     *
     * @var mixed[]
     */
    private $shadowProperties = Properties::PRESETS_OPTIONS[0];

    /**
     * Glow Properties.
     *
     * @var mixed[]
     */
    private $glowProperties = [
        'size' => null,
        'color' => [
            'type' => self::EXCEL_COLOR_TYPE_STANDARD,
            'value' => 'black',
            'alpha' => 40,
        ],
    ];

    /**
     * Soft Edge Properties.
     *
     * @var mixed[]
     */
    private $softEdges = [
        'size' => null,
    ];

    private const NUMERIC_FORMAT = [
        Properties::FORMAT_CODE_NUMBER,
        Properties::FORMAT_CODE_DATE,
    ];

    /**
     * Get Series Data Type.
     *
     * @param mixed $format_code
     */
    public function setAxisNumberProperties($format_code, ?bool $numeric = null): void
    {
        $format = (string) $format_code;
        $this->axisNumber['format'] = $format;
        $this->axisNumber['source_linked'] = 0;
        if (is_bool($numeric)) {
            $this->axisNumber['numeric'] = $numeric;
        } elseif (in_array($format, self::NUMERIC_FORMAT, true)) {
            $this->axisNumber['numeric'] = true;
        }
    }

    /**
     * Get Axis Number Format Data Type.
     *
     * @return string
     */
    public function getAxisNumberFormat()
    {
        return $this->axisNumber['format'];
    }

    /**
     * Get Axis Number Source Linked.
     *
     * @return string
     */
    public function getAxisNumberSourceLinked()
    {
        return (string) $this->axisNumber['source_linked'];
    }

    public function getAxisIsNumericFormat(): bool
    {
        return (bool) $this->axisNumber['numeric'];
    }

    /**
     * Set Axis Options Properties.
     *
     * @param string $axisLabels
     * @param string $horizontalCrossesValue
     * @param string $horizontalCrosses
     * @param string $axisOrientation
     * @param string $majorTmt
     * @param string $minorTmt
     * @param string $minimum
     * @param string $maximum
     * @param string $majorUnit
     * @param string $minorUnit
     */
    public function setAxisOptionsProperties($axisLabels, $horizontalCrossesValue = null, $horizontalCrosses = null, $axisOrientation = null, $majorTmt = null, $minorTmt = null, $minimum = null, $maximum = null, $majorUnit = null, $minorUnit = null): void
    {
        $this->axisOptions['axis_labels'] = (string) $axisLabels;
        ($horizontalCrossesValue !== null) ? $this->axisOptions['horizontal_crosses_value'] = (string) $horizontalCrossesValue : null;
        ($horizontalCrosses !== null) ? $this->axisOptions['horizontal_crosses'] = (string) $horizontalCrosses : null;
        ($axisOrientation !== null) ? $this->axisOptions['orientation'] = (string) $axisOrientation : null;
        ($majorTmt !== null) ? $this->axisOptions['major_tick_mark'] = (string) $majorTmt : null;
        ($minorTmt !== null) ? $this->axisOptions['minor_tick_mark'] = (string) $minorTmt : null;
        ($minorTmt !== null) ? $this->axisOptions['minor_tick_mark'] = (string) $minorTmt : null;
        ($minimum !== null) ? $this->axisOptions['minimum'] = (string) $minimum : null;
        ($maximum !== null) ? $this->axisOptions['maximum'] = (string) $maximum : null;
        ($majorUnit !== null) ? $this->axisOptions['major_unit'] = (string) $majorUnit : null;
        ($minorUnit !== null) ? $this->axisOptions['minor_unit'] = (string) $minorUnit : null;
    }

    /**
     * Get Axis Options Property.
     *
     * @param string $property
     *
     * @return string
     */
    public function getAxisOptionsProperty($property)
    {
        return $this->axisOptions[$property];
    }

    /**
     * Set Axis Orientation Property.
     *
     * @param string $orientation
     */
    public function setAxisOrientation($orientation): void
    {
        $this->axisOptions['orientation'] = (string) $orientation;
    }

    /**
     * Set Fill Property.
     *
     * @param string $color
     * @param int $alpha
     * @param string $AlphaType
     */
    public function setFillParameters($color, $alpha = 0, $AlphaType = self::EXCEL_COLOR_TYPE_ARGB): void
    {
        $this->fillProperties = $this->setColorProperties($color, $alpha, $AlphaType);
    }

    /**
     * Set Line Property.
     *
     * @param string $color
     * @param int $alpha
     * @param string $alphaType
     */
    public function setLineParameters($color, $alpha = 0, $alphaType = self::EXCEL_COLOR_TYPE_ARGB): void
    {
        $this->lineProperties = $this->setColorProperties($color, $alpha, $alphaType);
    }

    /**
     * Get Fill Property.
     *
     * @param string $property
     *
     * @return string
     */
    public function getFillProperty($property)
    {
        return $this->fillProperties[$property];
    }

    /**
     * Get Line Property.
     *
     * @param string $property
     *
     * @return string
     */
    public function getLineProperty($property)
    {
        return $this->lineProperties[$property];
    }

    /**
     * Set Line Style Properties.
     *
     * @param float $lineWidth
     * @param string $compoundType
     * @param string $dashType
     * @param string $capType
     * @param string $joinType
     * @param string $headArrowType
     * @param string $headArrowSize
     * @param string $endArrowType
     * @param string $endArrowSize
     */
    public function setLineStyleProperties($lineWidth = null, $compoundType = null, $dashType = null, $capType = null, $joinType = null, $headArrowType = null, $headArrowSize = null, $endArrowType = null, $endArrowSize = null): void
    {
        ($lineWidth !== null) ? $this->lineStyleProperties['width'] = $this->getExcelPointsWidth((float) $lineWidth) : null;
        ($compoundType !== null) ? $this->lineStyleProperties['compound'] = (string) $compoundType : null;
        ($dashType !== null) ? $this->lineStyleProperties['dash'] = (string) $dashType : null;
        ($capType !== null) ? $this->lineStyleProperties['cap'] = (string) $capType : null;
        ($joinType !== null) ? $this->lineStyleProperties['join'] = (string) $joinType : null;
        ($headArrowType !== null) ? $this->lineStyleProperties['arrow']['head']['type'] = (string) $headArrowType : null;
        ($headArrowSize !== null) ? $this->lineStyleProperties['arrow']['head']['size'] = (string) $headArrowSize : null;
        ($endArrowType !== null) ? $this->lineStyleProperties['arrow']['end']['type'] = (string) $endArrowType : null;
        ($endArrowSize !== null) ? $this->lineStyleProperties['arrow']['end']['size'] = (string) $endArrowSize : null;
    }

    /**
     * Get Line Style Property.
     *
     * @param array|string $elements
     *
     * @return string
     */
    public function getLineStyleProperty($elements)
    {
        return $this->getArrayElementsValue($this->lineStyleProperties, $elements);
    }

    /**
     * Get Line Style Arrow Excel Width.
     *
     * @param string $arrow
     *
     * @return string
     */
    public function getLineStyleArrowWidth($arrow)
    {
        return $this->getLineStyleArrowSize($this->lineStyleProperties['arrow'][$arrow]['size'], 'w');
    }

    /**
     * Get Line Style Arrow Excel Length.
     *
     * @param string $arrow
     *
     * @return string
     */
    public function getLineStyleArrowLength($arrow)
    {
        return $this->getLineStyleArrowSize($this->lineStyleProperties['arrow'][$arrow]['size'], 'len');
    }

    /**
     * @param mixed $value
     */
    public function setShadowProperty(string $propertyName, $value): self
    {
        if ($propertyName === 'color' && is_array($value)) {
            $this->setShadowColor($value['value'], $value['alpha'], $value['type']);
        } else {
            $this->shadowProperties[$propertyName] = $value;
        }

        return $this;
    }

    /**
     * Set Shadow Properties.
     *
     * @param int $shadowPresets
     * @param string $colorValue
     * @param string $colorType
     * @param null|int|string $colorAlpha
     * @param null|float $blur
     * @param null|int $angle
     * @param null|float $distance
     */
    public function setShadowProperties($shadowPresets, $colorValue = null, $colorType = null, $colorAlpha = null, $blur = null, $angle = null, $distance = null): void
    {
        $this->setShadowPresetsProperties((int) $shadowPresets)
            ->setShadowColor(
                $colorValue ?? $this->shadowProperties['color']['value'],
                (int) ($colorAlpha ?? $this->shadowProperties['color']['alpha']),
                $colorType ?? $this->shadowProperties['color']['type']
            )
            ->setShadowBlur($blur)
            ->setShadowAngle($angle)
            ->setShadowDistance($distance);
    }

    /**
     * Set Shadow Color.
     *
     * @param int $presets
     *
     * @return $this
     */
    private function setShadowPresetsProperties($presets)
    {
        $this->shadowProperties['presets'] = $presets;
        $this->setShadowPropertiesMapValues($this->getShadowPresetsMap($presets));

        return $this;
    }

    private const SHADOW_ARRAY_KEYS = ['size', 'color'];

    /**
     * Set Shadow Properties from Mapped Values.
     *
     * @param mixed $reference
     *
     * @return $this
     */
    private function setShadowPropertiesMapValues(array $propertiesMap, &$reference = null)
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
     * Set Shadow Color.
     *
     * @param null|string $color
     * @param null|int $alpha
     * @param null|string $alphaType
     *
     * @return $this
     */
    private function setShadowColor($color, $alpha, $alphaType)
    {
        $this->shadowProperties['color'] = $this->setColorProperties($color, $alpha, $alphaType);

        return $this;
    }

    /**
     * Set Shadow Blur.
     *
     * @param null|float $blur
     *
     * @return $this
     */
    private function setShadowBlur($blur)
    {
        if ($blur !== null) {
            $this->shadowProperties['blur'] = $blur;
        }

        return $this;
    }

    /**
     * Set Shadow Angle.
     *
     * @param null|float|int $angle
     *
     * @return $this
     */
    private function setShadowAngle($angle)
    {
        if (is_numeric($angle)) {
            $this->shadowProperties['direction'] = $angle;
        }

        return $this;
    }

    /**
     * Set Shadow Distance.
     *
     * @param null|float $distance
     *
     * @return $this
     */
    private function setShadowDistance($distance)
    {
        if ($distance !== null) {
            $this->shadowProperties['distance'] = $distance;
        }

        return $this;
    }

    /**
     * Get Shadow Property.
     *
     * @param string|string[] $elements
     *
     * @return null|array|int|string
     */
    public function getShadowProperty($elements)
    {
        return $this->getArrayElementsValue($this->shadowProperties, $elements);
    }

    /**
     * Set Glow Properties.
     *
     * @param float $size
     * @param null|string $colorValue
     * @param null|int $colorAlpha
     * @param null|string $colorType
     */
    public function setGlowProperties($size, $colorValue = null, $colorAlpha = null, $colorType = null): void
    {
        $this->setGlowSize($size)
            ->setGlowColor(
                $colorValue ?? $this->glowProperties['color']['value'],
                $colorAlpha ?? (int) $this->glowProperties['color']['alpha'],
                $colorType ?? $this->glowProperties['color']['type']
            );
    }

    /**
     * Get Glow Property.
     *
     * @param array|string $property
     *
     * @return null|string
     */
    public function getGlowProperty($property)
    {
        return $this->getArrayElementsValue($this->glowProperties, $property);
    }

    /**
     * Set Glow Color.
     *
     * @param float $size
     *
     * @return $this
     */
    private function setGlowSize($size)
    {
        if ($size !== null) {
            $this->glowProperties['size'] = $size;
        }

        return $this;
    }

    /**
     * Set Glow Color.
     *
     * @param string $color
     * @param int $alpha
     * @param string $colorType
     *
     * @return $this
     */
    private function setGlowColor($color, $alpha, $colorType)
    {
        $this->glowProperties['color'] = $this->setColorProperties($color, $alpha, $colorType);

        return $this;
    }

    /**
     * Set Soft Edges Size.
     *
     * @param float $size
     */
    public function setSoftEdges($size): void
    {
        if ($size !== null) {
            $this->softEdges['size'] = $size;
        }
    }

    /**
     * Get Soft Edges Size.
     *
     * @return string
     */
    public function getSoftEdgesSize()
    {
        return $this->softEdges['size'];
    }
}
