<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

/**
 * Created by PhpStorm.
 * User: Wiktor Trzonkowski
 * Date: 7/2/14
 * Time: 2:36 PM.
 */
class GridLines extends Properties
{
    /**
     * Properties of Class:
     * Object State (State for Minor Tick Mark) @var bool
     * Line Properties @var  array of mixed
     * Shadow Properties @var  array of mixed
     * Glow Properties @var  array of mixed
     * Soft Properties @var  array of mixed.
     */
    private $objectState = false;

    private $lineProperties = [
        'color' => [
            'type' => self::EXCEL_COLOR_TYPE_STANDARD,
            'value' => null,
            'alpha' => 0,
        ],
        'style' => [
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
        ],
    ];

    private $shadowProperties = [
        'presets' => self::SHADOW_PRESETS_NOSHADOW,
        'effect' => null,
        'color' => [
            'type' => self::EXCEL_COLOR_TYPE_STANDARD,
            'value' => 'black',
            'alpha' => 85,
        ],
        'size' => [
            'sx' => null,
            'sy' => null,
            'kx' => null,
        ],
        'blur' => null,
        'direction' => null,
        'distance' => null,
        'algn' => null,
        'rotWithShape' => null,
    ];

    private $glowProperties = [
        'size' => null,
        'color' => [
            'type' => self::EXCEL_COLOR_TYPE_STANDARD,
            'value' => 'black',
            'alpha' => 40,
        ],
    ];

    private $softEdges = [
        'size' => null,
    ];

    /**
     * Get Object State.
     *
     * @return bool
     */
    public function getObjectState()
    {
        return $this->objectState;
    }

    /**
     * Change Object State to True.
     *
     * @return $this
     */
    private function activateObject()
    {
        $this->objectState = true;

        return $this;
    }

    /**
     * Set Line Color Properties.
     *
     * @param string $value
     * @param int $alpha
     * @param string $colorType
     */
    public function setLineColorProperties($value, $alpha = 0, $colorType = self::EXCEL_COLOR_TYPE_STANDARD): void
    {
        $this->activateObject()
            ->lineProperties['color'] = $this->setColorProperties(
                $value,
                $alpha,
                $colorType
            );
    }

    /**
     * Set Line Color Properties.
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
        $this->activateObject();
        ($lineWidth !== null)
                ? $this->lineProperties['style']['width'] = $this->getExcelPointsWidth((float) $lineWidth)
                : null;
        ($compoundType !== null)
                ? $this->lineProperties['style']['compound'] = (string) $compoundType
                : null;
        ($dashType !== null)
                ? $this->lineProperties['style']['dash'] = (string) $dashType
                : null;
        ($capType !== null)
                ? $this->lineProperties['style']['cap'] = (string) $capType
                : null;
        ($joinType !== null)
                ? $this->lineProperties['style']['join'] = (string) $joinType
                : null;
        ($headArrowType !== null)
                ? $this->lineProperties['style']['arrow']['head']['type'] = (string) $headArrowType
                : null;
        ($headArrowSize !== null)
                ? $this->lineProperties['style']['arrow']['head']['size'] = (string) $headArrowSize
                : null;
        ($endArrowType !== null)
                ? $this->lineProperties['style']['arrow']['end']['type'] = (string) $endArrowType
                : null;
        ($endArrowSize !== null)
                ? $this->lineProperties['style']['arrow']['end']['size'] = (string) $endArrowSize
                : null;
    }

    /**
     * Get Line Color Property.
     *
     * @param string $propertyName
     *
     * @return string
     */
    public function getLineColorProperty($propertyName)
    {
        return $this->lineProperties['color'][$propertyName];
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
        return $this->getArrayElementsValue($this->lineProperties['style'], $elements);
    }

    /**
     * Set Glow Properties.
     *
     * @param float $size
     * @param string $colorValue
     * @param int $colorAlpha
     * @param string $colorType
     */
    public function setGlowProperties($size, $colorValue = null, $colorAlpha = null, $colorType = null): void
    {
        $this
            ->activateObject()
            ->setGlowSize($size)
            ->setGlowColor($colorValue, $colorAlpha, $colorType);
    }

    /**
     * Get Glow Color Property.
     *
     * @param string $propertyName
     *
     * @return string
     */
    public function getGlowColor($propertyName)
    {
        return $this->glowProperties['color'][$propertyName];
    }

    /**
     * Get Glow Size.
     *
     * @return string
     */
    public function getGlowSize()
    {
        return $this->glowProperties['size'];
    }

    /**
     * Set Glow Size.
     *
     * @param float $size
     *
     * @return $this
     */
    private function setGlowSize($size)
    {
        $this->glowProperties['size'] = $this->getExcelPointsWidth((float) $size);

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
        if ($color !== null) {
            $this->glowProperties['color']['value'] = (string) $color;
        }
        if ($alpha !== null) {
            $this->glowProperties['color']['alpha'] = $this->getTrueAlpha((int) $alpha);
        }
        if ($colorType !== null) {
            $this->glowProperties['color']['type'] = (string) $colorType;
        }

        return $this;
    }

    /**
     * Get Line Style Arrow Parameters.
     *
     * @param string $arrowSelector
     * @param string $propertySelector
     *
     * @return string
     */
    public function getLineStyleArrowParameters($arrowSelector, $propertySelector)
    {
        return $this->getLineStyleArrowSize($this->lineProperties['style']['arrow'][$arrowSelector]['size'], $propertySelector);
    }

    /**
     * Set Shadow Properties.
     *
     * @param int $presets
     * @param string $colorValue
     * @param string $colorType
     * @param string $colorAlpha
     * @param string $blur
     * @param int $angle
     * @param float $distance
     */
    public function setShadowProperties($presets, $colorValue = null, $colorType = null, $colorAlpha = null, $blur = null, $angle = null, $distance = null): void
    {
        $this->activateObject()
            ->setShadowPresetsProperties((int) $presets)
            ->setShadowColor(
                $colorValue ?? $this->shadowProperties['color']['value'],
                $colorAlpha === null ? (int) $this->shadowProperties['color']['alpha'] : $this->getTrueAlpha($colorAlpha),
                $colorType ?? $this->shadowProperties['color']['type']
            )
            ->setShadowBlur((float) $blur)
            ->setShadowAngle($angle)
            ->setShadowDistance($distance);
    }

    /**
     * Set Shadow Presets Properties.
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

    /**
     * Set Shadow Properties Values.
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
                if ($reference === null) {
                    $reference = &$this->shadowProperties[$property_key];
                } else {
                    $reference = &$reference[$property_key];
                }
                $this->setShadowPropertiesMapValues($property_val, $reference);
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
     * @param string $color
     * @param int $alpha
     * @param string $colorType
     *
     * @return $this
     */
    private function setShadowColor($color, $alpha, $colorType)
    {
        if ($color !== null) {
            $this->shadowProperties['color']['value'] = (string) $color;
        }
        if ($alpha !== null) {
            $this->shadowProperties['color']['alpha'] = $this->getTrueAlpha((int) $alpha);
        }
        if ($colorType !== null) {
            $this->shadowProperties['color']['type'] = (string) $colorType;
        }

        return $this;
    }

    /**
     * Set Shadow Blur.
     *
     * @param float $blur
     *
     * @return $this
     */
    private function setShadowBlur($blur)
    {
        if ($blur !== null) {
            $this->shadowProperties['blur'] = (string) $this->getExcelPointsWidth($blur);
        }

        return $this;
    }

    /**
     * Set Shadow Angle.
     *
     * @param int $angle
     *
     * @return $this
     */
    private function setShadowAngle($angle)
    {
        if ($angle !== null) {
            $this->shadowProperties['direction'] = (string) $this->getExcelPointsAngle($angle);
        }

        return $this;
    }

    /**
     * Set Shadow Distance.
     *
     * @param float $distance
     *
     * @return $this
     */
    private function setShadowDistance($distance)
    {
        if ($distance !== null) {
            $this->shadowProperties['distance'] = (string) $this->getExcelPointsWidth($distance);
        }

        return $this;
    }

    /**
     * Get Shadow Property.
     *
     * @param string|string[] $elements
     *
     * @return string
     */
    public function getShadowProperty($elements)
    {
        return $this->getArrayElementsValue($this->shadowProperties, $elements);
    }

    /**
     * Set Soft Edges Size.
     *
     * @param float $size
     */
    public function setSoftEdgesSize($size): void
    {
        if ($size !== null) {
            $this->activateObject();
            $this->softEdges['size'] = (string) $this->getExcelPointsWidth($size);
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
