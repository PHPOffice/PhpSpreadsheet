<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class Alignment extends Supervisor
{
    // Horizontal alignment styles
    const HORIZONTAL_GENERAL = 'general';
    const HORIZONTAL_LEFT = 'left';
    const HORIZONTAL_RIGHT = 'right';
    const HORIZONTAL_CENTER = 'center';
    const HORIZONTAL_CENTER_CONTINUOUS = 'centerContinuous';
    const HORIZONTAL_JUSTIFY = 'justify';
    const HORIZONTAL_FILL = 'fill';
    const HORIZONTAL_DISTRIBUTED = 'distributed'; // Excel2007 only

    // Vertical alignment styles
    const VERTICAL_BOTTOM = 'bottom';
    const VERTICAL_TOP = 'top';
    const VERTICAL_CENTER = 'center';
    const VERTICAL_JUSTIFY = 'justify';
    const VERTICAL_DISTRIBUTED = 'distributed'; // Excel2007 only

    // Read order
    const READORDER_CONTEXT = 0;
    const READORDER_LTR = 1;
    const READORDER_RTL = 2;

    // Special value for Text Rotation
    const TEXTROTATION_STACK_EXCEL = 255;
    const TEXTROTATION_STACK_PHPSPREADSHEET = -165; // 90 - 255

    /**
     * Horizontal alignment.
     *
     * @var string
     */
    protected $horizontal = self::HORIZONTAL_GENERAL;

    /**
     * Vertical alignment.
     *
     * @var string
     */
    protected $vertical = self::VERTICAL_BOTTOM;

    /**
     * Text rotation.
     *
     * @var int
     */
    protected $textRotation = 0;

    /**
     * Wrap text.
     *
     * @var bool
     */
    protected $wrapText = false;

    /**
     * Shrink to fit.
     *
     * @var bool
     */
    protected $shrinkToFit = false;

    /**
     * Indent - only possible with horizontal alignment left and right.
     *
     * @var int
     */
    protected $indent = 0;

    /**
     * Read order.
     *
     * @var int
     */
    protected $readOrder = 0;

    /**
     * Create a new Alignment.
     *
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *                                       Leave this value at default unless you understand exactly what
     *                                          its ramifications are
     * @param bool $isConditional Flag indicating if this is a conditional style or not
     *                                       Leave this value at default unless you understand exactly what
     *                                          its ramifications are
     */
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        // Supervisor?
        parent::__construct($isSupervisor);

        if ($isConditional) {
            $this->horizontal = null;
            $this->vertical = null;
            $this->textRotation = null;
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     *
     * @return Alignment
     */
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getAlignment();
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
        return ['alignment' => $array];
    }

    /**
     * Apply styles from array.
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->applyFromArray(
     *        [
     *            'horizontal'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
     *            'vertical'     => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
     *            'textRotation' => 0,
     *            'wrapText'     => TRUE
     *        ]
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
            $this->getActiveSheet()->getStyle($this->getSelectedCells())
                ->applyFromArray($this->getStyleArray($pStyles));
        } else {
            if (isset($pStyles['horizontal'])) {
                $this->setHorizontal($pStyles['horizontal']);
            }
            if (isset($pStyles['vertical'])) {
                $this->setVertical($pStyles['vertical']);
            }
            if (isset($pStyles['textRotation'])) {
                $this->setTextRotation($pStyles['textRotation']);
            }
            if (isset($pStyles['wrapText'])) {
                $this->setWrapText($pStyles['wrapText']);
            }
            if (isset($pStyles['shrinkToFit'])) {
                $this->setShrinkToFit($pStyles['shrinkToFit']);
            }
            if (isset($pStyles['indent'])) {
                $this->setIndent($pStyles['indent']);
            }
            if (isset($pStyles['readOrder'])) {
                $this->setReadOrder($pStyles['readOrder']);
            }
        }

        return $this;
    }

    /**
     * Get Horizontal.
     *
     * @return string
     */
    public function getHorizontal()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHorizontal();
        }

        return $this->horizontal;
    }

    /**
     * Set Horizontal.
     *
     * @param string $pValue see self::HORIZONTAL_*
     *
     * @return $this
     */
    public function setHorizontal($pValue)
    {
        if ($pValue == '') {
            $pValue = self::HORIZONTAL_GENERAL;
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['horizontal' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->horizontal = $pValue;
        }

        return $this;
    }

    /**
     * Get Vertical.
     *
     * @return string
     */
    public function getVertical()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getVertical();
        }

        return $this->vertical;
    }

    /**
     * Set Vertical.
     *
     * @param string $pValue see self::VERTICAL_*
     *
     * @return $this
     */
    public function setVertical($pValue)
    {
        if ($pValue == '') {
            $pValue = self::VERTICAL_BOTTOM;
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['vertical' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->vertical = $pValue;
        }

        return $this;
    }

    /**
     * Get TextRotation.
     *
     * @return int
     */
    public function getTextRotation()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getTextRotation();
        }

        return $this->textRotation;
    }

    /**
     * Set TextRotation.
     *
     * @param int $pValue
     *
     * @return $this
     */
    public function setTextRotation($pValue)
    {
        // Excel2007 value 255 => PhpSpreadsheet value -165
        if ($pValue == self::TEXTROTATION_STACK_EXCEL) {
            $pValue = self::TEXTROTATION_STACK_PHPSPREADSHEET;
        }

        // Set rotation
        if (($pValue >= -90 && $pValue <= 90) || $pValue == self::TEXTROTATION_STACK_PHPSPREADSHEET) {
            if ($this->isSupervisor) {
                $styleArray = $this->getStyleArray(['textRotation' => $pValue]);
                $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            } else {
                $this->textRotation = $pValue;
            }
        } else {
            throw new PhpSpreadsheetException('Text rotation should be a value between -90 and 90.');
        }

        return $this;
    }

    /**
     * Get Wrap Text.
     *
     * @return bool
     */
    public function getWrapText()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getWrapText();
        }

        return $this->wrapText;
    }

    /**
     * Set Wrap Text.
     *
     * @param bool $pValue
     *
     * @return $this
     */
    public function setWrapText($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['wrapText' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->wrapText = $pValue;
        }

        return $this;
    }

    /**
     * Get Shrink to fit.
     *
     * @return bool
     */
    public function getShrinkToFit()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getShrinkToFit();
        }

        return $this->shrinkToFit;
    }

    /**
     * Set Shrink to fit.
     *
     * @param bool $pValue
     *
     * @return $this
     */
    public function setShrinkToFit($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['shrinkToFit' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->shrinkToFit = $pValue;
        }

        return $this;
    }

    /**
     * Get indent.
     *
     * @return int
     */
    public function getIndent()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getIndent();
        }

        return $this->indent;
    }

    /**
     * Set indent.
     *
     * @param int $pValue
     *
     * @return $this
     */
    public function setIndent($pValue)
    {
        if ($pValue > 0) {
            if (
                $this->getHorizontal() != self::HORIZONTAL_GENERAL &&
                $this->getHorizontal() != self::HORIZONTAL_LEFT &&
                $this->getHorizontal() != self::HORIZONTAL_RIGHT
            ) {
                $pValue = 0; // indent not supported
            }
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['indent' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->indent = $pValue;
        }

        return $this;
    }

    /**
     * Get read order.
     *
     * @return int
     */
    public function getReadOrder()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getReadOrder();
        }

        return $this->readOrder;
    }

    /**
     * Set read order.
     *
     * @param int $pValue
     *
     * @return $this
     */
    public function setReadOrder($pValue)
    {
        if ($pValue < 0 || $pValue > 2) {
            $pValue = 0;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['readOrder' => $pValue]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->readOrder = $pValue;
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
            $this->horizontal .
            $this->vertical .
            $this->textRotation .
            ($this->wrapText ? 't' : 'f') .
            ($this->shrinkToFit ? 't' : 'f') .
            $this->indent .
            $this->readOrder .
            __CLASS__
        );
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'horizontal', $this->getHorizontal());
        $this->exportArray2($exportedArray, 'indent', $this->getIndent());
        $this->exportArray2($exportedArray, 'readOrder', $this->getReadOrder());
        $this->exportArray2($exportedArray, 'shrinkToFit', $this->getShrinkToFit());
        $this->exportArray2($exportedArray, 'textRotation', $this->getTextRotation());
        $this->exportArray2($exportedArray, 'vertical', $this->getVertical());
        $this->exportArray2($exportedArray, 'wrapText', $this->getWrapText());

        return $exportedArray;
    }
}
