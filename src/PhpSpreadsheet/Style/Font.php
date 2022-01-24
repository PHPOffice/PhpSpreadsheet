<?php

namespace PhpOffice\PhpSpreadsheet\Style;

class Font extends Supervisor
{
    // Underline types
    const UNDERLINE_NONE = 'none';
    const UNDERLINE_DOUBLE = 'double';
    const UNDERLINE_DOUBLEACCOUNTING = 'doubleAccounting';
    const UNDERLINE_SINGLE = 'single';
    const UNDERLINE_SINGLEACCOUNTING = 'singleAccounting';

    /**
     * Font Name.
     *
     * @var null|string
     */
    protected $name = 'Calibri';

    /**
     * Font Size.
     *
     * @var null|float
     */
    protected $size = 11;

    /**
     * Bold.
     *
     * @var null|bool
     */
    protected $bold = false;

    /**
     * Italic.
     *
     * @var null|bool
     */
    protected $italic = false;

    /**
     * Superscript.
     *
     * @var null|bool
     */
    protected $superscript = false;

    /**
     * Subscript.
     *
     * @var null|bool
     */
    protected $subscript = false;

    /**
     * Underline.
     *
     * @var null|string
     */
    protected $underline = self::UNDERLINE_NONE;

    /**
     * Strikethrough.
     *
     * @var null|bool
     */
    protected $strikethrough = false;

    /**
     * Foreground color.
     *
     * @var Color
     */
    protected $color;

    /**
     * @var null|int
     */
    public $colorIndex;

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
    public function __construct($isSupervisor = false, $isConditional = false)
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
     *
     * @return Font
     */
    public function getSharedComponent()
    {
        /** @var Style */
        $parent = $this->parent;

        return $parent->getSharedComponent()->getFont();
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
    public function applyFromArray(array $styleArray)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($styleArray));
        } else {
            if (isset($styleArray['name'])) {
                $this->setName($styleArray['name']);
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
        }

        return $this;
    }

    /**
     * Get Name.
     *
     * @return null|string
     */
    public function getName()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getName();
        }

        return $this->name;
    }

    /**
     * Set Name.
     *
     * @param string $fontname
     *
     * @return $this
     */
    public function setName($fontname)
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

        return $this;
    }

    /**
     * Get Size.
     *
     * @return null|float
     */
    public function getSize()
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
    public function setSize($sizeInPoints)
    {
        if (is_string($sizeInPoints) || is_int($sizeInPoints)) {
            $sizeInPoints = (float) $sizeInPoints; // $pValue = 0 if given string is not numeric
        }

        // Size must be a positive floating point number
        // ECMA-376-1:2016, part 1, chapter 18.4.11 sz (Font Size), p. 1536
        if (!is_float($sizeInPoints) || !($sizeInPoints > 0)) {
            $sizeInPoints = 10.0;
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
     *
     * @return null|bool
     */
    public function getBold()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getBold();
        }

        return $this->bold;
    }

    /**
     * Set Bold.
     *
     * @param bool $bold
     *
     * @return $this
     */
    public function setBold($bold)
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
     *
     * @return null|bool
     */
    public function getItalic()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getItalic();
        }

        return $this->italic;
    }

    /**
     * Set Italic.
     *
     * @param bool $italic
     *
     * @return $this
     */
    public function setItalic($italic)
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
     *
     * @return null|bool
     */
    public function getSuperscript()
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
    public function setSuperscript(bool $superscript)
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
     *
     * @return null|bool
     */
    public function getSubscript()
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
    public function setSubscript(bool $subscript)
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

    /**
     * Get Underline.
     *
     * @return null|string
     */
    public function getUnderline()
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
    public function setUnderline($underlineStyle)
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
     *
     * @return null|bool
     */
    public function getStrikethrough()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getStrikethrough();
        }

        return $this->strikethrough;
    }

    /**
     * Set Strikethrough.
     *
     * @param bool $strikethru
     *
     * @return $this
     */
    public function setStrikethrough($strikethru)
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
     *
     * @return Color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set Color.
     *
     * @return $this
     */
    public function setColor(Color $color)
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
            $this->name .
            $this->size .
            ($this->bold ? 't' : 'f') .
            ($this->italic ? 't' : 'f') .
            ($this->superscript ? 't' : 'f') .
            ($this->subscript ? 't' : 'f') .
            $this->underline .
            ($this->strikethrough ? 't' : 'f') .
            $this->color->getHashCode() .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'bold', $this->getBold());
        $this->exportArray2($exportedArray, 'color', $this->getColor());
        $this->exportArray2($exportedArray, 'italic', $this->getItalic());
        $this->exportArray2($exportedArray, 'name', $this->getName());
        $this->exportArray2($exportedArray, 'size', $this->getSize());
        $this->exportArray2($exportedArray, 'strikethrough', $this->getStrikethrough());
        $this->exportArray2($exportedArray, 'subscript', $this->getSubscript());
        $this->exportArray2($exportedArray, 'superscript', $this->getSuperscript());
        $this->exportArray2($exportedArray, 'underline', $this->getUnderline());

        return $exportedArray;
    }
}
