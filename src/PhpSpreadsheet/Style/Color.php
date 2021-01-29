<?php

namespace PhpOffice\PhpSpreadsheet\Style;

class Color extends Supervisor
{
    const NAMED_COLORS = [
        'Black',
        'White',
        'Red',
        'Green',
        'Blue',
        'Yellow',
        'Magenta',
        'Cyan',
    ];

    // Colors
    const COLOR_BLACK = 'FF000000';
    const COLOR_WHITE = 'FFFFFFFF';
    const COLOR_RED = 'FFFF0000';
    const COLOR_DARKRED = 'FF800000';
    const COLOR_BLUE = 'FF0000FF';
    const COLOR_DARKBLUE = 'FF000080';
    const COLOR_GREEN = 'FF00FF00';
    const COLOR_DARKGREEN = 'FF008000';
    const COLOR_YELLOW = 'FFFFFF00';
    const COLOR_DARKYELLOW = 'FF808000';

    /**
     * Indexed colors array.
     *
     * @var array
     */
    protected static $indexedColors;

    /**
     * ARGB - Alpha RGB.
     *
     * @var string
     */
    protected $argb;

    /**
     * Create a new Color.
     *
     * @param string $pARGB ARGB value for the colour
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     * @param bool $isConditional Flag indicating if this is a conditional style or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct($pARGB = self::COLOR_BLACK, $isSupervisor = false, $isConditional = false)
    {
        //    Supervisor?
        parent::__construct($isSupervisor);

        //    Initialise values
        if (!$isConditional) {
            $this->argb = $pARGB;
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     *
     * @return Color
     */
    public function getSharedComponent()
    {
        if ($this->parentPropertyName === 'endColor') {
            return $this->parent->getSharedComponent()->getEndColor();
        }
        if ($this->parentPropertyName === 'startColor') {
            return $this->parent->getSharedComponent()->getStartColor();
        }

        return $this->parent->getSharedComponent()->getColor();
    }

    /**
     * Build style array from subcomponents.
     *
     * @param array $array
     *
     * @return array
     */
    public function getStyleArray($array)
    {
        return $this->parent->getStyleArray([$this->parentPropertyName => $array]);
    }

    /**
     * Apply styles from array.
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->getColor()->applyFromArray(['rgb' => '808080']);
     * </code>
     *
     * @param array $pStyles Array containing style information
     *
     * @return $this
     */
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['rgb'])) {
                $this->setRGB($pStyles['rgb']);
            }
            if (isset($pStyles['argb'])) {
                $this->setARGB($pStyles['argb']);
            }
        }

        return $this;
    }

    /**
     * Get ARGB.
     *
     * @return string
     */
    public function getARGB()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getARGB();
        }

        return $this->argb;
    }

    /**
     * Set ARGB.
     *
     * @param string $pValue see self::COLOR_*
     *
     * @return $this
     */
    public function setARGB($pValue)
    {
        if ($pValue == '') {
            $pValue = self::COLOR_BLACK;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['argb' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->argb = $pValue;
        }

        return $this;
    }

    /**
     * Get RGB.
     *
     * @return string
     */
    public function getRGB()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getRGB();
        }

        return substr($this->argb, 2);
    }

    /**
     * Set RGB.
     *
     * @param string $pValue RGB value
     *
     * @return $this
     */
    public function setRGB($pValue)
    {
        if ($pValue == '') {
            $pValue = '000000';
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['argb' => 'FF' . $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->argb = 'FF' . $pValue;
        }

        return $this;
    }

    /**
     * Get a specified colour component of an RGB value.
     *
     * @param string $RGB The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param int $offset Position within the RGB value to extract
     * @param bool $hex Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     *
     * @return string The extracted colour component
     */
    private static function getColourComponent($RGB, $offset, $hex = true)
    {
        $colour = substr($RGB, $offset, 2);

        return ($hex) ? $colour : hexdec($colour);
    }

    /**
     * Get the red colour component of an RGB value.
     *
     * @param string $RGB The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param bool $hex Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     *
     * @return string The red colour component
     */
    public static function getRed($RGB, $hex = true)
    {
        return self::getColourComponent($RGB, strlen($RGB) - 6, $hex);
    }

    /**
     * Get the green colour component of an RGB value.
     *
     * @param string $RGB The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param bool $hex Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     *
     * @return string The green colour component
     */
    public static function getGreen($RGB, $hex = true)
    {
        return self::getColourComponent($RGB, strlen($RGB) - 4, $hex);
    }

    /**
     * Get the blue colour component of an RGB value.
     *
     * @param string $RGB The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param bool $hex Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     *
     * @return string The blue colour component
     */
    public static function getBlue($RGB, $hex = true)
    {
        return self::getColourComponent($RGB, strlen($RGB) - 2, $hex);
    }

    /**
     * Adjust the brightness of a color.
     *
     * @param string $hex The colour as an RGBA or RGB value (e.g. FF00CCCC or CCDDEE)
     * @param float $adjustPercentage The percentage by which to adjust the colour as a float from -1 to 1
     *
     * @return string The adjusted colour as an RGBA or RGB value (e.g. FF00CCCC or CCDDEE)
     */
    public static function changeBrightness($hex, $adjustPercentage)
    {
        $rgba = (strlen($hex) === 8);
        $adjustPercentage = max(-1.0, min(1.0, $adjustPercentage));

        $red = self::getRed($hex, false);
        $green = self::getGreen($hex, false);
        $blue = self::getBlue($hex, false);
        if ($adjustPercentage > 0) {
            $red += (255 - $red) * $adjustPercentage;
            $green += (255 - $green) * $adjustPercentage;
            $blue += (255 - $blue) * $adjustPercentage;
        } else {
            $red += $red * $adjustPercentage;
            $green += $green * $adjustPercentage;
            $blue += $blue * $adjustPercentage;
        }

        $rgb = strtoupper(
            str_pad(dechex((int) $red), 2, '0', 0) .
            str_pad(dechex((int) $green), 2, '0', 0) .
            str_pad(dechex((int) $blue), 2, '0', 0)
        );

        return (($rgba) ? 'FF' : '') . $rgb;
    }

    /**
     * Get indexed color.
     *
     * @param int $pIndex Index entry point into the colour array
     * @param bool $background Flag to indicate whether default background or foreground colour
     *                                            should be returned if the indexed colour doesn't exist
     *
     * @return self
     */
    public static function indexedColor($pIndex, $background = false)
    {
        // Clean parameter
        $pIndex = (int) $pIndex;

        // Indexed colors
        if (self::$indexedColors === null) {
            self::$indexedColors = [
                1 => 'FF000000', //  System Colour #1 - Black
                2 => 'FFFFFFFF', //  System Colour #2 - White
                3 => 'FFFF0000', //  System Colour #3 - Red
                4 => 'FF00FF00', //  System Colour #4 - Green
                5 => 'FF0000FF', //  System Colour #5 - Blue
                6 => 'FFFFFF00', //  System Colour #6 - Yellow
                7 => 'FFFF00FF', //  System Colour #7- Magenta
                8 => 'FF00FFFF', //  System Colour #8- Cyan
                9 => 'FF800000', //  Standard Colour #9
                10 => 'FF008000', //  Standard Colour #10
                11 => 'FF000080', //  Standard Colour #11
                12 => 'FF808000', //  Standard Colour #12
                13 => 'FF800080', //  Standard Colour #13
                14 => 'FF008080', //  Standard Colour #14
                15 => 'FFC0C0C0', //  Standard Colour #15
                16 => 'FF808080', //  Standard Colour #16
                17 => 'FF9999FF', //  Chart Fill Colour #17
                18 => 'FF993366', //  Chart Fill Colour #18
                19 => 'FFFFFFCC', //  Chart Fill Colour #19
                20 => 'FFCCFFFF', //  Chart Fill Colour #20
                21 => 'FF660066', //  Chart Fill Colour #21
                22 => 'FFFF8080', //  Chart Fill Colour #22
                23 => 'FF0066CC', //  Chart Fill Colour #23
                24 => 'FFCCCCFF', //  Chart Fill Colour #24
                25 => 'FF000080', //  Chart Line Colour #25
                26 => 'FFFF00FF', //  Chart Line Colour #26
                27 => 'FFFFFF00', //  Chart Line Colour #27
                28 => 'FF00FFFF', //  Chart Line Colour #28
                29 => 'FF800080', //  Chart Line Colour #29
                30 => 'FF800000', //  Chart Line Colour #30
                31 => 'FF008080', //  Chart Line Colour #31
                32 => 'FF0000FF', //  Chart Line Colour #32
                33 => 'FF00CCFF', //  Standard Colour #33
                34 => 'FFCCFFFF', //  Standard Colour #34
                35 => 'FFCCFFCC', //  Standard Colour #35
                36 => 'FFFFFF99', //  Standard Colour #36
                37 => 'FF99CCFF', //  Standard Colour #37
                38 => 'FFFF99CC', //  Standard Colour #38
                39 => 'FFCC99FF', //  Standard Colour #39
                40 => 'FFFFCC99', //  Standard Colour #40
                41 => 'FF3366FF', //  Standard Colour #41
                42 => 'FF33CCCC', //  Standard Colour #42
                43 => 'FF99CC00', //  Standard Colour #43
                44 => 'FFFFCC00', //  Standard Colour #44
                45 => 'FFFF9900', //  Standard Colour #45
                46 => 'FFFF6600', //  Standard Colour #46
                47 => 'FF666699', //  Standard Colour #47
                48 => 'FF969696', //  Standard Colour #48
                49 => 'FF003366', //  Standard Colour #49
                50 => 'FF339966', //  Standard Colour #50
                51 => 'FF003300', //  Standard Colour #51
                52 => 'FF333300', //  Standard Colour #52
                53 => 'FF993300', //  Standard Colour #53
                54 => 'FF993366', //  Standard Colour #54
                55 => 'FF333399', //  Standard Colour #55
                56 => 'FF333333', //  Standard Colour #56
            ];
        }

        if (isset(self::$indexedColors[$pIndex])) {
            return new self(self::$indexedColors[$pIndex]);
        }

        if ($background) {
            return new self(self::COLOR_WHITE);
        }

        return new self(self::COLOR_BLACK);
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }

        return md5(
            $this->argb .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'argb', $this->getARGB());

        return $exportedArray;
    }
}
