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
     * @param string $horizontalAlignment see self::HORIZONTAL_*
     *
     * @return $this
     */
    public function setHorizontal(string $horizontalAlignment)
    {
        if ($horizontalAlignment == '') {
            $horizontalAlignment = self::HORIZONTAL_GENERAL;
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['horizontal' => $horizontalAlignment]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->horizontal = $horizontalAlignment;
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
     * @param string $verticalAlignment see self::VERTICAL_*
     *
     * @return $this
     */
    public function setVertical($verticalAlignment)
    {
        if ($verticalAlignment == '') {
            $verticalAlignment = self::VERTICAL_BOTTOM;
        }

        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['vertical' => $verticalAlignment]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->vertical = $verticalAlignment;
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
     * @param int $rotation
     *
     * @return $this
     */
    public function setTextRotation($rotation)
    {
        // Excel2007 value 255 => PhpSpreadsheet value -165
        if ($rotation == 255) {
            $rotation = -165;
        }

        // Set rotation
        if (($rotation >= -90 && $rotation <= 90) || $rotation == -165) {
            if ($this->isSupervisor) {
                $styleArray = $this->getStyleArray(['textRotation' => $rotation]);
                $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            } else {
                $this->textRotation = $rotation;
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
     * @param bool $wrapped
     *
     * @return $this
     */
    public function setWrapText($wrapped)
    {
        if ($wrapped == '') {
            $wrapped = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['wrapText' => $wrapped]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->wrapText = $wrapped;
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
     * @param bool $shrink
     *
     * @return $this
     */
    public function setShrinkToFit($shrink)
    {
        if ($shrink == '') {
            $shrink = false;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['shrinkToFit' => $shrink]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->shrinkToFit = $shrink;
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
     * @param int $indent
     *
     * @return $this
     */
    public function setIndent($indent)
    {
        if ($indent > 0) {
            if (
                $this->getHorizontal() != self::HORIZONTAL_GENERAL &&
                $this->getHorizontal() != self::HORIZONTAL_LEFT &&
                $this->getHorizontal() != self::HORIZONTAL_RIGHT
            ) {
                $indent = 0; // indent not supported
            }
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['indent' => $indent]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->indent = $indent;
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
     * @param int $readOrder
     *
     * @return $this
     */
    public function setReadOrder($readOrder)
    {
        if ($readOrder < 0 || $readOrder > 2) {
            $readOrder = 0;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(['readOrder' => $readOrder]);
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->readOrder = $readOrder;
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
}
