<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Chart\ChartColor;

class Font extends Supervisor
{
    // Underline types
    const UNDERLINE_NONE = 'none';
    const UNDERLINE_DOUBLE = 'double';
    const UNDERLINE_DOUBLEACCOUNTING = 'doubleAccounting';
    const UNDERLINE_SINGLE = 'single';
    const UNDERLINE_SINGLEACCOUNTING = 'singleAccounting';

    const CAP_ALL = 'all';
    const CAP_SMALL = 'small';
    const CAP_NONE = 'none';
    private const VALID_CAPS = [self::CAP_ALL, self::CAP_SMALL, self::CAP_NONE];

    protected ?string $cap = null;

    /**
     * Font Name.
     */
    protected ?string $name = 'Calibri';

    /**
     * The following 7 are used only for chart titles, I think.
     */
    private string $latin = '';

    private string $eastAsian = '';

    private string $complexScript = '';

    private int $baseLine = 0;

    private string $strikeType = '';

    private ?ChartColor $underlineColor = null;

    private ?ChartColor $chartColor = null;
    // end of chart title items

    /**
     * Font Size.
     */
    protected ?float $size = 11;

    /**
     * Bold.
     */
    protected ?bool $bold = false;

    /**
     * Italic.
     */
    protected ?bool $italic = false;

    /**
     * Superscript.
     */
    protected ?bool $superscript = false;

    /**
     * Subscript.
     */
    protected ?bool $subscript = false;

    /**
     * Underline.
     */
    protected ?string $underline = self::UNDERLINE_NONE;

    /**
     * Strikethrough.
     */
    protected ?bool $strikethrough = false;

    /**
     * Foreground color.
     */
    protected Color $color;

    public ?int $colorIndex = null;

    protected string $scheme = '';

    /**
     * Create a new Font.
     *
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     * @param bool $isConditional Flag indicating if this is a conditional style or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct(bool $isSupervisor = false, bool $isConditional = false)
    {
        // Supervisor?
        parent::__construct($isSupervisor);

        // Initialise values
        if ($isConditional) {
            $this->name = null;
            $this->size = null;
            $this->bold = null;
            $this->italic = null;
            $this->superscript = null;
            $this->subscript = null;
            $this->underline = null;
            $this->strikethrough = null;
            $this->color = new Color(Color::COLOR_BLACK, $isSupervisor, $isConditional);
        } else {
            $this->color = new Color(Color::COLOR_BLACK, $isSupervisor);
        }
        // bind parent if we are a supervisor
        if ($isSupervisor) {
            $this->color->bindParent($this, 'color');
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

        return $parent->getSharedComponent()->getFont();
    }

    /**
     * Build style array from subcomponents.
     */
    public function getStyleArray(array $array): array
    {
        return ['font' => $array];
    }

    /**
     * Apply styles from array.
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->applyFromArray(
     *     [
     *         'name' => 'Arial',
     *         'bold' => TRUE,
     *         'italic' => FALSE,
     *         'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE,
     *         'strikethrough' => FALSE,
     *         'color' => [
     *             'rgb' => '808080'
     *         ]
     *     ]
     * );
     * </code>
     *
     * @param array $styleArray Array containing style information
     *
     * @return $this
     */
    public function applyFromArray(array $styleArray): static
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($styleArray));
        } else {
            if (isset($styleArray['name'])) {
                $this->setName($styleArray['name']);
            }
            if (isset($styleArray['latin'])) {
                $this->setLatin($styleArray['latin']);
            }
            if (isset($styleArray['eastAsian'])) {
                $this->setEastAsian($styleArray['eastAsian']);
            }
            if (isset($styleArray['complexScript'])) {
                $this->setComplexScript($styleArray['complexScript']);
            }
            if (isset($styleArray['bold'])) {
                $this->setBold($styleArray['bold']);
            }
            if (isset($styleArray['italic'])) {
                $this->setItalic($styleArray['italic']);
            }
            if (isset($styleArray['superscript'])) {
                $this->setSuperscript($styleArray['superscript']);
            }
            if (isset($styleArray['subscript'])) {
                $this->setSubscript($styleArray['subscript']);
            }
            if (isset($styleArray['underline'])) {
                $this->setUnderline($styleArray['underline']);
            }
            if (isset($styleArray['strikethrough'])) {
                $this->setStrikethrough($styleArray['strikethrough']);
            }
            if (isset($styleArray['color'])) {
                $this->getColor()->applyFromArray($styleArray['color']);
            }
            if (isset($styleArray['size'])) {
                $this->setSize($styleArray['size']);
            }
            if (isset($styleArray['chartColor'])) {
                $this->chartColor = $styleArray['chartColor'];
            }
            if (isset($styleArray['scheme'])) {
                $this->setScheme($styleArray['scheme']);
            }
            if (isset($styleArray['cap'])) {
                $this->setCap($styleArray['cap']);
            }
        }

        return $this;
    }

    /**
     * Get Name.
     */
    public function getName(): ?string
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getName();
        }

        return $this->name;
    }

    public function getLatin(): string
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getLatin();
        }

        return $this->latin;
    }

    public function getEastAsian(): string
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getEastAsian();
        }

        return $this->eastAsian;
    }

    public function getComplexScript(): string
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getComplexScript();
        }

        return $this->complexScript;
    }

    /**
     * Set Name and turn off Scheme.
     */
    public function setName(string $fontname): self
    {
        if ($fontname == '') {
            $fontname = 'Calibri';
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['name' => $fontname]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->name = $fontname;
        }

        return $this->setScheme('');
    }

    public function setLatin(string $fontname): self
    {
        if ($fontname == '') {
            $fontname = 'Calibri';
        }
        if (!$this->isSupervisor) {
            $this->latin = $fontname;
        } else {
            // should never be true
            // @codeCoverageIgnoreStart
            $styleArray = $this->getStyleArray(['latin' => $fontname]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            // @codeCoverageIgnoreEnd
        }

        return $this;
    }

    public function setEastAsian(string $fontname): self
    {
        if ($fontname == '') {
            $fontname = 'Calibri';
        }
        if (!$this->isSupervisor) {
            $this->eastAsian = $fontname;
        } else {
            // should never be true
            // @codeCoverageIgnoreStart
            $styleArray = $this->getStyleArray(['eastAsian' => $fontname]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            // @codeCoverageIgnoreEnd
        }

        return $this;
    }

    public function setComplexScript(string $fontname): self
    {
        if ($fontname == '') {
            $fontname = 'Calibri';
        }
        if (!$this->isSupervisor) {
            $this->complexScript = $fontname;
        } else {
            // should never be true
            // @codeCoverageIgnoreStart
            $styleArray = $this->getStyleArray(['complexScript' => $fontname]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            // @codeCoverageIgnoreEnd
        }

        return $this;
    }

    /**
     * Get Size.
     */
    public function getSize(): ?float
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getSize();
        }

        return $this->size;
    }

    /**
     * Set Size.
     *
     * @param mixed $sizeInPoints A float representing the value of a positive measurement in points (1/72 of an inch)
     *
     * @return $this
     */
    public function setSize(mixed $sizeInPoints, bool $nullOk = false): static
    {
        if (is_string($sizeInPoints) || is_int($sizeInPoints)) {
            $sizeInPoints = (float) $sizeInPoints; // $pValue = 0 if given string is not numeric
        }

        // Size must be a positive floating point number
        // ECMA-376-1:2016, part 1, chapter 18.4.11 sz (Font Size), p. 1536
        if (!is_float($sizeInPoints) || !($sizeInPoints > 0)) {
            if (!$nullOk || $sizeInPoints !== null) {
                $sizeInPoints = 10.0;
            }
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['size' => $sizeInPoints]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->size = $sizeInPoints;
        }

        return $this;
    }

    /**
     * Get Bold.
     */
    public function getBold(): ?bool
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getBold();
        }

        return $this->bold;
    }

    /**
     * Set Bold.
     *
     * @return $this
     */
    public function setBold(bool $bold): static
    {
        if ($bold == '') {
            $bold = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['bold' => $bold]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->bold = $bold;
        }

        return $this;
    }

    /**
     * Get Italic.
     */
    public function getItalic(): ?bool
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getItalic();
        }

        return $this->italic;
    }

    /**
     * Set Italic.
     *
     * @return $this
     */
    public function setItalic(bool $italic): static
    {
        if ($italic == '') {
            $italic = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['italic' => $italic]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->italic = $italic;
        }

        return $this;
    }

    /**
     * Get Superscript.
     */
    public function getSuperscript(): ?bool
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getSuperscript();
        }

        return $this->superscript;
    }

    /**
     * Set Superscript.
     *
     * @return $this
     */
    public function setSuperscript(bool $superscript): static
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['superscript' => $superscript]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->superscript = $superscript;
            if ($this->superscript) {
                $this->subscript = false;
            }
        }

        return $this;
    }

    /**
     * Get Subscript.
     */
    public function getSubscript(): ?bool
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getSubscript();
        }

        return $this->subscript;
    }

    /**
     * Set Subscript.
     *
     * @return $this
     */
    public function setSubscript(bool $subscript): static
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['subscript' => $subscript]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->subscript = $subscript;
            if ($this->subscript) {
                $this->superscript = false;
            }
        }

        return $this;
    }

    public function getBaseLine(): int
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getBaseLine();
        }

        return $this->baseLine;
    }

    public function setBaseLine(int $baseLine): self
    {
        if (!$this->isSupervisor) {
            $this->baseLine = $baseLine;
        } else {
            // should never be true
            // @codeCoverageIgnoreStart
            $styleArray = $this->getStyleArray(['baseLine' => $baseLine]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            // @codeCoverageIgnoreEnd
        }

        return $this;
    }

    public function getStrikeType(): string
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getStrikeType();
        }

        return $this->strikeType;
    }

    public function setStrikeType(string $strikeType): self
    {
        if (!$this->isSupervisor) {
            $this->strikeType = $strikeType;
        } else {
            // should never be true
            // @codeCoverageIgnoreStart
            $styleArray = $this->getStyleArray(['strikeType' => $strikeType]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            // @codeCoverageIgnoreEnd
        }

        return $this;
    }

    public function getUnderlineColor(): ?ChartColor
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getUnderlineColor();
        }

        return $this->underlineColor;
    }

    public function setUnderlineColor(array $colorArray): self
    {
        if (!$this->isSupervisor) {
            $this->underlineColor = new ChartColor($colorArray);
        } else {
            // should never be true
            // @codeCoverageIgnoreStart
            $styleArray = $this->getStyleArray(['underlineColor' => $colorArray]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            // @codeCoverageIgnoreEnd
        }

        return $this;
    }

    public function getChartColor(): ?ChartColor
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getChartColor();
        }

        return $this->chartColor;
    }

    public function setChartColor(array $colorArray): self
    {
        if (!$this->isSupervisor) {
            $this->chartColor = new ChartColor($colorArray);
        } else {
            // should never be true
            // @codeCoverageIgnoreStart
            $styleArray = $this->getStyleArray(['chartColor' => $colorArray]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            // @codeCoverageIgnoreEnd
        }

        return $this;
    }

    public function setChartColorFromObject(?ChartColor $chartColor): self
    {
        $this->chartColor = $chartColor;

        return $this;
    }

    /**
     * Get Underline.
     */
    public function getUnderline(): ?string
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getUnderline();
        }

        return $this->underline;
    }

    /**
     * Set Underline.
     *
     * @param bool|string $underlineStyle \PhpOffice\PhpSpreadsheet\Style\Font underline type
     *                                    If a boolean is passed, then TRUE equates to UNDERLINE_SINGLE,
     *                                        false equates to UNDERLINE_NONE
     *
     * @return $this
     */
    public function setUnderline($underlineStyle): static
    {
        if (is_bool($underlineStyle)) {
            $underlineStyle = ($underlineStyle) ? self::UNDERLINE_SINGLE : self::UNDERLINE_NONE;
        } elseif ($underlineStyle == '') {
            $underlineStyle = self::UNDERLINE_NONE;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['underline' => $underlineStyle]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->underline = $underlineStyle;
        }

        return $this;
    }

    /**
     * Get Strikethrough.
     */
    public function getStrikethrough(): ?bool
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getStrikethrough();
        }

        return $this->strikethrough;
    }

    /**
     * Set Strikethrough.
     *
     * @return $this
     */
    public function setStrikethrough(bool $strikethru): static
    {
        if ($strikethru == '') {
            $strikethru = false;
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['strikethrough' => $strikethru]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->strikethrough = $strikethru;
        }

        return $this;
    }

    /**
     * Get Color.
     */
    public function getColor(): Color
    {
        return $this->color;
    }

    /**
     * Set Color.
     *
     * @return $this
     */
    public function setColor(Color $color): static
    {
        // make sure parameter is a real color and not a supervisor
        $color = $color->getIsSupervisor() ? $color->getSharedComponent() : $color;

        if ($this->isSupervisor) {
            $styleArray = $this->getColor()->getStyleArray(['argb' => $color->getARGB()]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->color = $color;
        }

        return $this;
    }

    private function hashChartColor(?ChartColor $underlineColor): string
    {
        if ($underlineColor === null) {
            return '';
        }

        return
            $underlineColor->getValue()
            . $underlineColor->getType()
            . (string) $underlineColor->getAlpha();
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
            $this->name
            . $this->size
            . ($this->bold ? 't' : 'f')
            . ($this->italic ? 't' : 'f')
            . ($this->superscript ? 't' : 'f')
            . ($this->subscript ? 't' : 'f')
            . $this->underline
            . ($this->strikethrough ? 't' : 'f')
            . $this->color->getHashCode()
            . $this->scheme
            . implode(
                '*',
                [
                    $this->latin,
                    $this->eastAsian,
                    $this->complexScript,
                    $this->strikeType,
                    $this->hashChartColor($this->chartColor),
                    $this->hashChartColor($this->underlineColor),
                    (string) $this->baseLine,
                    (string) $this->cap,
                ]
            )
            . __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'baseLine', $this->getBaseLine());
        $this->exportArray2($exportedArray, 'bold', $this->getBold());
        $this->exportArray2($exportedArray, 'cap', $this->getCap());
        $this->exportArray2($exportedArray, 'chartColor', $this->getChartColor());
        $this->exportArray2($exportedArray, 'color', $this->getColor());
        $this->exportArray2($exportedArray, 'complexScript', $this->getComplexScript());
        $this->exportArray2($exportedArray, 'eastAsian', $this->getEastAsian());
        $this->exportArray2($exportedArray, 'italic', $this->getItalic());
        $this->exportArray2($exportedArray, 'latin', $this->getLatin());
        $this->exportArray2($exportedArray, 'name', $this->getName());
        $this->exportArray2($exportedArray, 'scheme', $this->getScheme());
        $this->exportArray2($exportedArray, 'size', $this->getSize());
        $this->exportArray2($exportedArray, 'strikethrough', $this->getStrikethrough());
        $this->exportArray2($exportedArray, 'strikeType', $this->getStrikeType());
        $this->exportArray2($exportedArray, 'subscript', $this->getSubscript());
        $this->exportArray2($exportedArray, 'superscript', $this->getSuperscript());
        $this->exportArray2($exportedArray, 'underline', $this->getUnderline());
        $this->exportArray2($exportedArray, 'underlineColor', $this->getUnderlineColor());

        return $exportedArray;
    }

    public function getScheme(): string
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getScheme();
        }

        return $this->scheme;
    }

    public function setScheme(string $scheme): self
    {
        if ($scheme === '' || $scheme === 'major' || $scheme === 'minor') {
            if ($this->isSupervisor) {
                $styleArray = $this->getStyleArray(['scheme' => $scheme]);
                $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            } else {
                $this->scheme = $scheme;
            }
        }

        return $this;
    }

    /**
     * Set capitalization attribute. If not one of the permitted
     * values (all, small, or none), set it to null.
     * This will be honored only for the font for chart titles.
     * None is distinguished from null because null will inherit
     * the current value, whereas 'none' will override it.
     */
    public function setCap(string $cap): self
    {
        $this->cap = in_array($cap, self::VALID_CAPS, true) ? $cap : null;

        return $this;
    }

    public function getCap(): ?string
    {
        return $this->cap;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $this->color = clone $this->color;
        $this->chartColor = ($this->chartColor === null) ? null : clone $this->chartColor;
        $this->underlineColor = ($this->underlineColor === null) ? null : clone $this->underlineColor;
    }
}
