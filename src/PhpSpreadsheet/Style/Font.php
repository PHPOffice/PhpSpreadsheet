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
        return $this->parent->getSharedComponent()->getFont();
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
     * @param array $pStyles Array containing style information
     *
     * @return $this
     */
    public function applyFromArray(array $pStyles)
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['name'])) {
                $this->setName($pStyles['name']);
            }
            if (isset($pStyles['bold'])) {
                $this->setBold($pStyles['bold']);
            }
            if (isset($pStyles['italic'])) {
                $this->setItalic($pStyles['italic']);
            }
            if (isset($pStyles['superscript'])) {
                $this->setSuperscript($pStyles['superscript']);
            }
            if (isset($pStyles['subscript'])) {
                $this->setSubscript($pStyles['subscript']);
            }
            if (isset($pStyles['underline'])) {
                $this->setUnderline($pStyles['underline']);
            }
            if (isset($pStyles['strikethrough'])) {
                $this->setStrikethrough($pStyles['strikethrough']);
            }
            if (isset($pStyles['color'])) {
                $this->getColor()->applyFromArray($pStyles['color']);
            }
            if (isset($pStyles['size'])) {
                $this->setSize($pStyles['size']);
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
     * @param string $pValue
     *
     * @return $this
     */
    public function setName($pValue)
    {
        if ($pValue == '') {
            $pValue = 'Calibri';
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['name' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->name = $pValue;
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
     * @param float $pValue
     *
     * @return $this
     */
    public function setSize($pValue)
    {
        if ($pValue == '') {
            $pValue = 10;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['size' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->size = $pValue;
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
     * @param bool $pValue
     *
     * @return $this
     */
    public function setBold($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['bold' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->bold = $pValue;
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
     * @param bool $pValue
     *
     * @return $this
     */
    public function setItalic($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['italic' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->italic = $pValue;
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
    public function setSuperscript(bool $pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['superscript' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->superscript = $pValue;
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
    public function setSubscript(bool $pValue)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['subscript' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->subscript = $pValue;
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
     * @param bool|string $pValue \PhpOffice\PhpSpreadsheet\Style\Font underline type
     *                                    If a boolean is passed, then TRUE equates to UNDERLINE_SINGLE,
     *                                        false equates to UNDERLINE_NONE
     *
     * @return $this
     */
    public function setUnderline($pValue)
    {
        if (is_bool($pValue)) {
            $pValue = ($pValue) ? self::UNDERLINE_SINGLE : self::UNDERLINE_NONE;
        } elseif ($pValue == '') {
            $pValue = self::UNDERLINE_NONE;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['underline' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->underline = $pValue;
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
     * @param bool $pValue
     *
     * @return $this
     */
    public function setStrikethrough($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['strikethrough' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->strikethrough = $pValue;
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
    public function setColor(Color $pValue)
    {
        // make sure parameter is a real color and not a supervisor
        $color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;

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
