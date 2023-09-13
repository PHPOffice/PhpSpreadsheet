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
    private const HORIZONTAL_CENTER_CONTINUOUS_LC = 'centercontinuous';
    // Mapping for horizontal alignment
    const HORIZONTAL_ALIGNMENT_FOR_XLSX = [
        self::HORIZONTAL_LEFT => self::HORIZONTAL_LEFT,
        self::HORIZONTAL_RIGHT => self::HORIZONTAL_RIGHT,
        self::HORIZONTAL_CENTER => self::HORIZONTAL_CENTER,
        self::HORIZONTAL_CENTER_CONTINUOUS => self::HORIZONTAL_CENTER_CONTINUOUS,
        self::HORIZONTAL_JUSTIFY => self::HORIZONTAL_JUSTIFY,
        self::HORIZONTAL_FILL => self::HORIZONTAL_FILL,
        self::HORIZONTAL_DISTRIBUTED => self::HORIZONTAL_DISTRIBUTED,
    ];
    // Mapping for horizontal alignment CSS
    const HORIZONTAL_ALIGNMENT_FOR_HTML = [
        self::HORIZONTAL_LEFT => self::HORIZONTAL_LEFT,
        self::HORIZONTAL_RIGHT => self::HORIZONTAL_RIGHT,
        self::HORIZONTAL_CENTER => self::HORIZONTAL_CENTER,
        self::HORIZONTAL_CENTER_CONTINUOUS => self::HORIZONTAL_CENTER,
        self::HORIZONTAL_JUSTIFY => self::HORIZONTAL_JUSTIFY,
        //self::HORIZONTAL_FILL => self::HORIZONTAL_FILL, // no reasonable equivalent for fill
        self::HORIZONTAL_DISTRIBUTED => self::HORIZONTAL_JUSTIFY,
    ];

    // Vertical alignment styles
    const VERTICAL_BOTTOM = 'bottom';
    const VERTICAL_TOP = 'top';
    const VERTICAL_CENTER = 'center';
    const VERTICAL_JUSTIFY = 'justify';
    const VERTICAL_DISTRIBUTED = 'distributed'; // Excel2007 only
    // Vertical alignment CSS
    private const VERTICAL_BASELINE = 'baseline';
    private const VERTICAL_MIDDLE = 'middle';
    private const VERTICAL_SUB = 'sub';
    private const VERTICAL_SUPER = 'super';
    private const VERTICAL_TEXT_BOTTOM = 'text-bottom';
    private const VERTICAL_TEXT_TOP = 'text-top';

    // Mapping for vertical alignment
    const VERTICAL_ALIGNMENT_FOR_XLSX = [
        self::VERTICAL_BOTTOM => self::VERTICAL_BOTTOM,
        self::VERTICAL_TOP => self::VERTICAL_TOP,
        self::VERTICAL_CENTER => self::VERTICAL_CENTER,
        self::VERTICAL_JUSTIFY => self::VERTICAL_JUSTIFY,
        self::VERTICAL_DISTRIBUTED => self::VERTICAL_DISTRIBUTED,
        // css settings that arent't in sync with Excel
        self::VERTICAL_BASELINE => self::VERTICAL_BOTTOM,
        self::VERTICAL_MIDDLE => self::VERTICAL_CENTER,
        self::VERTICAL_SUB => self::VERTICAL_BOTTOM,
        self::VERTICAL_SUPER => self::VERTICAL_TOP,
        self::VERTICAL_TEXT_BOTTOM => self::VERTICAL_BOTTOM,
        self::VERTICAL_TEXT_TOP => self::VERTICAL_TOP,
    ];

    // Mapping for vertical alignment for Html
    const VERTICAL_ALIGNMENT_FOR_HTML = [
        self::VERTICAL_BOTTOM => self::VERTICAL_BOTTOM,
        self::VERTICAL_TOP => self::VERTICAL_TOP,
        self::VERTICAL_CENTER => self::VERTICAL_MIDDLE,
        self::VERTICAL_JUSTIFY => self::VERTICAL_MIDDLE,
        self::VERTICAL_DISTRIBUTED => self::VERTICAL_MIDDLE,
        // css settings that arent't in sync with Excel
        self::VERTICAL_BASELINE => self::VERTICAL_BASELINE,
        self::VERTICAL_MIDDLE => self::VERTICAL_MIDDLE,
        self::VERTICAL_SUB => self::VERTICAL_SUB,
        self::VERTICAL_SUPER => self::VERTICAL_SUPER,
        self::VERTICAL_TEXT_BOTTOM => self::VERTICAL_TEXT_BOTTOM,
        self::VERTICAL_TEXT_TOP => self::VERTICAL_TEXT_TOP,
    ];

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
     * @var null|string
     */
    protected $horizontal = self::HORIZONTAL_GENERAL;

    /**
     * Vertical alignment.
     *
     * @var null|string
     */
    protected $vertical = self::VERTICAL_BOTTOM;

    /**
     * Text rotation.
     *
     * @var null|int
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
        /** @var Style */
        $parent = $this->parent;

        return $parent->getSharedComponent()->getAlignment();
    }

    /**
     * Build style array from subcomponents.
     *
     * @param array $array
     */
    public function getStyleArray($array): array
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
     * @param array $styleArray Array containing style information
     *
     * @return $this
     */
    public function applyFromArray(array $styleArray): static
    {
        if ($this->isSupervisor) {
            $this->getActiveSheet()->getStyle($this->getSelectedCells())
                ->applyFromArray($this->getStyleArray($styleArray));
        } else {
            if (isset($styleArray['horizontal'])) {
                $this->setHorizontal($styleArray['horizontal']);
            }
            if (isset($styleArray['vertical'])) {
                $this->setVertical($styleArray['vertical']);
            }
            if (isset($styleArray['textRotation'])) {
                $this->setTextRotation($styleArray['textRotation']);
            }
            if (isset($styleArray['wrapText'])) {
                $this->setWrapText($styleArray['wrapText']);
            }
            if (isset($styleArray['shrinkToFit'])) {
                $this->setShrinkToFit($styleArray['shrinkToFit']);
            }
            if (isset($styleArray['indent'])) {
                $this->setIndent($styleArray['indent']);
            }
            if (isset($styleArray['readOrder'])) {
                $this->setReadOrder($styleArray['readOrder']);
            }
        }

        return $this;
    }

    /**
     * Get Horizontal.
     *
     * @return null|string
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
    public function setHorizontal(string $horizontalAlignment): static
    {
        $horizontalAlignment = strtolower($horizontalAlignment);
        if ($horizontalAlignment === self::HORIZONTAL_CENTER_CONTINUOUS_LC) {
            $horizontalAlignment = self::HORIZONTAL_CENTER_CONTINUOUS;
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
     * @return null|string
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
    public function setVertical($verticalAlignment): static
    {
        $verticalAlignment = strtolower($verticalAlignment);

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
     * @return null|int
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
     * @param int $angleInDegrees
     *
     * @return $this
     */
    public function setTextRotation($angleInDegrees): static
    {
        // Excel2007 value 255 => PhpSpreadsheet value -165
        if ($angleInDegrees == self::TEXTROTATION_STACK_EXCEL) {
            $angleInDegrees = self::TEXTROTATION_STACK_PHPSPREADSHEET;
        }

        // Set rotation
        if (($angleInDegrees >= -90 && $angleInDegrees <= 90) || $angleInDegrees == self::TEXTROTATION_STACK_PHPSPREADSHEET) {
            if ($this->isSupervisor) {
                $styleArray = $this->getStyleArray(['textRotation' => $angleInDegrees]);
                $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
            } else {
                $this->textRotation = $angleInDegrees;
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
    public function setWrapText($wrapped): static
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
    public function setShrinkToFit($shrink): static
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
    public function setIndent($indent): static
    {
        if ($indent > 0) {
            if (
                $this->getHorizontal() != self::HORIZONTAL_GENERAL
                && $this->getHorizontal() != self::HORIZONTAL_LEFT
                && $this->getHorizontal() != self::HORIZONTAL_RIGHT
                && $this->getHorizontal() != self::HORIZONTAL_DISTRIBUTED
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
    public function setReadOrder($readOrder): static
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
            $this->horizontal
            . $this->vertical
            . $this->textRotation
            . ($this->wrapText ? 't' : 'f')
            . ($this->shrinkToFit ? 't' : 'f')
            . $this->indent
            . $this->readOrder
            . __CLASS__
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
