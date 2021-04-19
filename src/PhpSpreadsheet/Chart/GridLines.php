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
     * @param string $type
     */
    public function setLineColorProperties($value, $alpha = 0, $type = self::EXCEL_COLOR_TYPE_STANDARD): void
    {
        $this->activateObject()
            ->lineProperties['color'] = $this->setColorProperties(
                $value,
                $alpha,
                $type
            );
    }

    /**
     * Set Line Color Properties.
     *
     * @param float $line_width
     * @param string $compound_type
     * @param string $dash_type
     * @param string $cap_type
     * @param string $join_type
     * @param string $head_arrow_type
     * @param string $head_arrow_size
     * @param string $end_arrow_type
     * @param string $end_arrow_size
     */
    public function setLineStyleProperties($line_width = null, $compound_type = null, $dash_type = null, $cap_type = null, $join_type = null, $head_arrow_type = null, $head_arrow_size = null, $end_arrow_type = null, $end_arrow_size = null): void
    {
        $this->activateObject();
        ($line_width !== null)
                ? $this->lineProperties['style']['width'] = $this->getExcelPointsWidth((float) $line_width)
                : null;
        ($compound_type !== null)
                ? $this->lineProperties['style']['compound'] = (string) $compound_type
                : null;
        ($dash_type !== null)
                ? $this->lineProperties['style']['dash'] = (string) $dash_type
                : null;
        ($cap_type !== null)
                ? $this->lineProperties['style']['cap'] = (string) $cap_type
                : null;
        ($join_type !== null)
                ? $this->lineProperties['style']['join'] = (string) $join_type
                : null;
        ($head_arrow_type !== null)
                ? $this->lineProperties['style']['arrow']['head']['type'] = (string) $head_arrow_type
                : null;
        ($head_arrow_size !== null)
                ? $this->lineProperties['style']['arrow']['head']['size'] = (string) $head_arrow_size
                : null;
        ($end_arrow_type !== null)
                ? $this->lineProperties['style']['arrow']['end']['type'] = (string) $end_arrow_type
                : null;
        ($end_arrow_size !== null)
                ? $this->lineProperties['style']['arrow']['end']['size'] = (string) $end_arrow_size
                : null;
    }

    /**
     * Get Line Color Property.
     *
     * @param string $parameter
     *
     * @return string
     */
    public function getLineColorProperty($parameter)
    {
        return $this->lineProperties['color'][$parameter];
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
     * @param string $color_value
     * @param int $color_alpha
     * @param string $color_type
     */
    public function setGlowProperties($size, $color_value = null, $color_alpha = null, $color_type = null): void
    {
        $this
            ->activateObject()
            ->setGlowSize($size)
            ->setGlowColor($color_value, $color_alpha, $color_type);
    }

    /**
     * Get Glow Color Property.
     *
     * @param string $property
     *
     * @return string
     */
    public function getGlowColor($property)
    {
        return $this->glowProperties['color'][$property];
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
     * @param string $type
     *
     * @return $this
     */
    private function setGlowColor($color, $alpha, $type)
    {
        if ($color !== null) {
            $this->glowProperties['color']['value'] = (string) $color;
        }
        if ($alpha !== null) {
            $this->glowProperties['color']['alpha'] = $this->getTrueAlpha((int) $alpha);
        }
        if ($type !== null) {
            $this->glowProperties['color']['type'] = (string) $type;
        }

        return $this;
    }

    /**
     * Get Line Style Arrow Parameters.
     *
     * @param string $arrow_selector
     * @param string $property_selector
     *
     * @return string
     */
    public function getLineStyleArrowParameters($arrow_selector, $property_selector)
    {
        return $this->getLineStyleArrowSize($this->lineProperties['style']['arrow'][$arrow_selector]['size'], $property_selector);
    }

    /**
     * Set Shadow Properties.
     *
     * @param int $sh_presets
     * @param string $sh_color_value
     * @param string $sh_color_type
     * @param int $sh_color_alpha
     * @param string $sh_blur
     * @param int $sh_angle
     * @param float $sh_distance
     */
    public function setShadowProperties($sh_presets, $sh_color_value = null, $sh_color_type = null, $sh_color_alpha = null, $sh_blur = null, $sh_angle = null, $sh_distance = null): void
    {
        $this->activateObject()
            ->setShadowPresetsProperties((int) $sh_presets)
            ->setShadowColor(
                $sh_color_value ?? $this->shadowProperties['color']['value'],
                $sh_color_alpha === null ? (int) $this->shadowProperties['color']['alpha'] : $this->getTrueAlpha($sh_color_alpha),
                $sh_color_type ?? $this->shadowProperties['color']['type']
            )
            ->setShadowBlur($sh_blur)
            ->setShadowAngle($sh_angle)
            ->setShadowDistance($sh_distance);
    }

    /**
     * Set Shadow Presets Properties.
     *
     * @param int $shadow_presets
     *
     * @return $this
     */
    private function setShadowPresetsProperties($shadow_presets)
    {
        $this->shadowProperties['presets'] = $shadow_presets;
        $this->setShadowProperiesMapValues($this->getShadowPresetsMap($shadow_presets));

        return $this;
    }

    /**
     * Set Shadow Properties Values.
     *
     * @param mixed $reference
     *
     * @return $this
     */
    private function setShadowProperiesMapValues(array $properties_map, &$reference = null)
    {
        $base_reference = $reference;
        foreach ($properties_map as $property_key => $property_val) {
            if (is_array($property_val)) {
                if ($reference === null) {
                    $reference = &$this->shadowProperties[$property_key];
                } else {
                    $reference = &$reference[$property_key];
                }
                $this->setShadowProperiesMapValues($property_val, $reference);
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
     * @param string $type
     *
     * @return $this
     */
    private function setShadowColor($color, $alpha, $type)
    {
        if ($color !== null) {
            $this->shadowProperties['color']['value'] = (string) $color;
        }
        if ($alpha !== null) {
            $this->shadowProperties['color']['alpha'] = $this->getTrueAlpha((int) $alpha);
        }
        if ($type !== null) {
            $this->shadowProperties['color']['type'] = (string) $type;
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
