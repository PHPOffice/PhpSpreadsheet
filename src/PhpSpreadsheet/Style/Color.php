<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Theme;

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
    const COLOR_MAGENTA = 'FFFF00FF';
    const COLOR_CYAN = 'FF00FFFF';

    const NAMED_COLOR_TRANSLATIONS = [
        'Black' => self::COLOR_BLACK,
        'White' => self::COLOR_WHITE,
        'Red' => self::COLOR_RED,
        'Green' => self::COLOR_GREEN,
        'Blue' => self::COLOR_BLUE,
        'Yellow' => self::COLOR_YELLOW,
        'Magenta' => self::COLOR_MAGENTA,
        'Cyan' => self::COLOR_CYAN,
    ];

    const VALIDATE_ARGB_SIZE = 8;
    const VALIDATE_RGB_SIZE = 6;
    const VALIDATE_COLOR_6 = '/^[A-F0-9]{6}$/i';
    const VALIDATE_COLOR_8 = '/^[A-F0-9]{8}$/i';

    private const INDEXED_COLORS = [
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

    /**
     * ARGB - Alpha RGB.
     */
    protected ?string $argb = null;

    private bool $hasChanged = false;

    private int $theme = -1;

    /**
     * Create a new Color.
     *
     * @param string $colorValue ARGB value for the colour, or named colour
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     * @param bool $isConditional Flag indicating if this is a conditional style or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct(string $colorValue = self::COLOR_BLACK, bool $isSupervisor = false, bool $isConditional = false)
    {
        //    Supervisor?
        parent::__construct($isSupervisor);

        //    Initialise values
        if (!$isConditional) {
            $this->argb = $this->validateColor($colorValue) ?: self::COLOR_BLACK;
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     */
    public function getSharedComponent(): self
    {
        /** @var Style $parent */
        $parent = $this->parent;
        /** @var Border|Fill $sharedComponent */
        $sharedComponent = $parent->getSharedComponent();
        if ($sharedComponent instanceof Fill) {
            if ($this->parentPropertyName === 'endColor') {
                return $sharedComponent->getEndColor();
            }

            return $sharedComponent->getStartColor();
        }

        return $sharedComponent->getColor();
    }

    /**
     * Build style array from subcomponents.
     *
     * @param mixed[] $array
     *
     * @return mixed[]
     */
    public function getStyleArray(array $array): array
    {
        /** @var Style $parent */
        $parent = $this->parent;

        return $parent->getStyleArray([$this->parentPropertyName => $array]);
    }

    /**
     * Apply styles from array.
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->getColor()->applyFromArray(['rgb' => '808080']);
     * </code>
     *
     * @param array{rgb?: string, argb?: string, theme?: int} $styleArray Array containing style information
     *
     * @return $this
     */
    public function applyFromArray(array $styleArray): static
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()
                ->getStyle($this->getSelectedCells())
                ->applyFromArray(
                    $this->getStyleArray($styleArray)
                );
        } else {
            if (isset($styleArray['rgb'])) {
                $this->setRGB($styleArray['rgb']);
            }
            if (isset($styleArray['argb'])) {
                $this->setARGB($styleArray['argb']);
            }
            if (isset($styleArray['theme'])) {
                $this->setTheme($styleArray['theme']);
            }
        }

        return $this;
    }

    private function validateColor(?string $colorValue): string
    {
        if ($colorValue === null || $colorValue === '') {
            return self::COLOR_BLACK;
        }
        $named = ucfirst(strtolower($colorValue));
        if (array_key_exists($named, self::NAMED_COLOR_TRANSLATIONS)) {
            return self::NAMED_COLOR_TRANSLATIONS[$named];
        }
        if (preg_match(self::VALIDATE_COLOR_8, $colorValue) === 1) {
            return $colorValue;
        }
        if (preg_match(self::VALIDATE_COLOR_6, $colorValue) === 1) {
            return 'FF' . $colorValue;
        }

        return '';
    }

    /**
     * Get ARGB.
     */
    public function getARGB(): ?string
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getARGB();
        }

        return $this->argb;
    }

    /**
     * Set ARGB.
     *
     * @param ?string $colorValue  ARGB value, or a named color
     *
     * @return $this
     */
    public function setARGB(?string $colorValue = self::COLOR_BLACK, bool $nullStringOkay = false): static
    {
        $this->hasChanged = true;
        $this->setTheme(-1);
        if (!$nullStringOkay || $colorValue !== '') {
            $colorValue = $this->validateColor($colorValue);
            if ($colorValue === '') {
                return $this;
            }
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['argb' => $colorValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->argb = $colorValue;
        }

        return $this;
    }

    /**
     * Get RGB.
     */
    public function getRGB(): string
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getRGB();
        }

        return substr($this->argb ?? '', 2);
    }

    /**
     * Set RGB.
     *
     * @param ?string $colorValue RGB value, or a named color
     *
     * @return $this
     */
    public function setRGB(?string $colorValue = self::COLOR_BLACK): static
    {
        return $this->setARGB($colorValue);
    }

    /**
     * Get a specified colour component of an RGB value.
     *
     * @param string $rgbValue The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param int $offset Position within the RGB value to extract
     * @param bool $hex Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     *
     * @return int|string The extracted colour component
     */
    private static function getColourComponent(string $rgbValue, int $offset, bool $hex = true): string|int
    {
        $colour = substr($rgbValue, $offset, 2) ?: '';
        if (preg_match('/^[0-9a-f]{2}$/i', $colour) !== 1) {
            $colour = '00';
        }

        return ($hex) ? $colour : (int) hexdec($colour);
    }

    /**
     * Get the red colour component of an RGB value.
     *
     * @param string $rgbValue The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param bool $hex Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     *
     * @return int|string The red colour component
     */
    public static function getRed(string $rgbValue, bool $hex = true)
    {
        return self::getColourComponent($rgbValue, strlen($rgbValue) - 6, $hex);
    }

    /**
     * Get the green colour component of an RGB value.
     *
     * @param string $rgbValue The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param bool $hex Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     *
     * @return int|string The green colour component
     */
    public static function getGreen(string $rgbValue, bool $hex = true)
    {
        return self::getColourComponent($rgbValue, strlen($rgbValue) - 4, $hex);
    }

    /**
     * Get the blue colour component of an RGB value.
     *
     * @param string $rgbValue The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param bool $hex Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     *
     * @return int|string The blue colour component
     */
    public static function getBlue(string $rgbValue, bool $hex = true)
    {
        return self::getColourComponent($rgbValue, strlen($rgbValue) - 2, $hex);
    }

    /**
     * Adjust the brightness of a color.
     *
     * @param string $hexColourValue The colour as an RGBA or RGB value (e.g. FF00CCCC or CCDDEE)
     * @param float $adjustPercentage The percentage by which to adjust the colour as a float from -1 to 1
     *
     * @return string The adjusted colour as an RGBA or RGB value (e.g. FF00CCCC or CCDDEE)
     */
    public static function changeBrightness(string $hexColourValue, float $adjustPercentage): string
    {
        $rgba = (strlen($hexColourValue) === 8);
        $adjustPercentage = max(-1.0, min(1.0, $adjustPercentage));

        /** @var int $red */
        $red = self::getRed($hexColourValue, false);
        /** @var int $green */
        $green = self::getGreen($hexColourValue, false);
        /** @var int $blue */
        $blue = self::getBlue($hexColourValue, false);

        return (($rgba) ? 'FF' : '') . RgbTint::rgbAndTintToRgb($red, $green, $blue, $adjustPercentage);
    }

    /**
     * Get indexed color.
     *
     * @param int $colorIndex Index entry point into the colour array
     * @param bool $background Flag to indicate whether default background or foreground colour
     *                                            should be returned if the indexed colour doesn't exist
     * @param null|string[] $palette
     */
    public static function indexedColor(int $colorIndex, bool $background = false, ?array $palette = null): self
    {
        // Clean parameter
        $colorIndex = (int) $colorIndex;

        if (empty($palette)) {
            if (isset(self::INDEXED_COLORS[$colorIndex])) {
                return new self(self::INDEXED_COLORS[$colorIndex]);
            }
        } else {
            if (isset($palette[$colorIndex])) {
                return new self($palette[$colorIndex]);
            }
        }

        return ($background) ? new self(self::COLOR_WHITE) : new self(self::COLOR_BLACK);
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }

        return md5(
            $this->argb
            . (string) $this->theme
            . __CLASS__
        );
    }

    /** @return mixed[] */
    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'argb', $this->getARGB());
        $this->exportArray2($exportedArray, 'theme', $this->getTheme());

        return $exportedArray;
    }

    public function getHasChanged(): bool
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->hasChanged;
        }

        return $this->hasChanged;
    }

    public function getTheme(): int
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getTheme();
        }

        return $this->theme;
    }

    public function setTheme(int $theme): self
    {
        $this->hasChanged = true;

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['theme' => $theme]);
            $this->getActiveSheet()
                ->getStyle($this->getSelectedCells())
                ->applyFromArray($styleArray);
        } else {
            $this->theme = $theme;
        }

        return $this;
    }

    public function setHyperlinkTheme(): self
    {
        $rgb = $this->getActiveSheet()
            ->getParent()
            ?->getTheme()
            ->getThemeColors();
        if (is_array($rgb) && array_key_exists('hlink', $rgb)) {
            $this->setRGB($rgb['hlink']);
        }

        return $this->setTheme(Theme::HYPERLINK_THEME);
    }
}
