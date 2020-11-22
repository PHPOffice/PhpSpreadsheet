<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Style extends Supervisor
{
    /**
     * Font.
     *
     * @var Font
     */
    protected $font;

    /**
     * Fill.
     *
     * @var Fill
     */
    protected $fill;

    /**
     * Borders.
     *
     * @var Borders
     */
    protected $borders;

    /**
     * Alignment.
     *
     * @var Alignment
     */
    protected $alignment;

    /**
     * Number Format.
     *
     * @var NumberFormat
     */
    protected $numberFormat;

    /**
     * Protection.
     *
     * @var Protection
     */
    protected $protection;

    /**
     * Index of style in collection. Only used for real style.
     *
     * @var int
     */
    protected $index;

    /**
     * Use Quote Prefix when displaying in cell editor. Only used for real style.
     *
     * @var bool
     */
    protected $quotePrefix = false;

    /**
     * Create a new Style.
     *
     * @param bool $isSupervisor Flag indicating if this is a supervisor or not
     *         Leave this value at default unless you understand exactly what
     *    its ramifications are
     * @param bool $isConditional Flag indicating if this is a conditional style or not
     *       Leave this value at default unless you understand exactly what
     *    its ramifications are
     */
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        parent::__construct($isSupervisor);

        // Initialise values
        $this->font = new Font($isSupervisor, $isConditional);
        $this->fill = new Fill($isSupervisor, $isConditional);
        $this->borders = new Borders($isSupervisor, $isConditional);
        $this->alignment = new Alignment($isSupervisor, $isConditional);
        $this->numberFormat = new NumberFormat($isSupervisor, $isConditional);
        $this->protection = new Protection($isSupervisor, $isConditional);

        // bind parent if we are a supervisor
        if ($isSupervisor) {
            $this->font->bindParent($this);
            $this->fill->bindParent($this);
            $this->borders->bindParent($this);
            $this->alignment->bindParent($this);
            $this->numberFormat->bindParent($this);
            $this->protection->bindParent($this);
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor.
     *
     * @return Style
     */
    public function getSharedComponent()
    {
        $activeSheet = $this->getActiveSheet();
        $selectedCell = $this->getActiveCell(); // e.g. 'A1'

        if ($activeSheet->cellExists($selectedCell)) {
            $xfIndex = $activeSheet->getCell($selectedCell)->getXfIndex();
        } else {
            $xfIndex = 0;
        }

        return $this->parent->getCellXfByIndex($xfIndex);
    }

    /**
     * Get parent. Only used for style supervisor.
     *
     * @return Spreadsheet
     */
    public function getParent()
    {
        return $this->parent;
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
        return ['quotePrefix' => $array];
    }

    /**
     * Apply styles from array.
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->applyFromArray(
     *     [
     *         'font' => [
     *             'name' => 'Arial',
     *             'bold' => true,
     *             'italic' => false,
     *             'underline' => Font::UNDERLINE_DOUBLE,
     *             'strikethrough' => false,
     *             'color' => [
     *                 'rgb' => '808080'
     *             ]
     *         ],
     *         'borders' => [
     *             'bottom' => [
     *                 'borderStyle' => Border::BORDER_DASHDOT,
     *                 'color' => [
     *                     'rgb' => '808080'
     *                 ]
     *             ],
     *             'top' => [
     *                 'borderStyle' => Border::BORDER_DASHDOT,
     *                 'color' => [
     *                     'rgb' => '808080'
     *                 ]
     *             ]
     *         ],
     *         'alignment' => [
     *             'horizontal' => Alignment::HORIZONTAL_CENTER,
     *             'vertical' => Alignment::VERTICAL_CENTER,
     *             'wrapText' => true,
     *         ],
     *         'quotePrefix'    => true
     *     ]
     * );
     * </code>
     *
     * @param array $pStyles Array containing style information
     * @param bool $pAdvanced advanced mode for setting borders
     *
     * @return $this
     */
    public function applyFromArray(array $pStyles, $pAdvanced = true)
    {
        if ($this->isSupervisor) {
            $pRange = $this->getSelectedCells();

            // Uppercase coordinate
            $pRange = strtoupper($pRange);

            // Is it a cell range or a single cell?
            if (strpos($pRange, ':') === false) {
                $rangeA = $pRange;
                $rangeB = $pRange;
            } else {
                [$rangeA, $rangeB] = explode(':', $pRange);
            }

            // Calculate range outer borders
            $rangeStart = Coordinate::coordinateFromString($rangeA);
            $rangeEnd = Coordinate::coordinateFromString($rangeB);

            // Translate column into index
            $rangeStart0 = $rangeStart[0];
            $rangeEnd0 = $rangeEnd[0];
            $rangeStart[0] = Coordinate::columnIndexFromString($rangeStart[0]);
            $rangeEnd[0] = Coordinate::columnIndexFromString($rangeEnd[0]);

            // Make sure we can loop upwards on rows and columns
            if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
                $tmp = $rangeStart;
                $rangeStart = $rangeEnd;
                $rangeEnd = $tmp;
            }

            // ADVANCED MODE:
            if ($pAdvanced && isset($pStyles['borders'])) {
                // 'allBorders' is a shorthand property for 'outline' and 'inside' and
                //        it applies to components that have not been set explicitly
                if (isset($pStyles['borders']['allBorders'])) {
                    foreach (['outline', 'inside'] as $component) {
                        if (!isset($pStyles['borders'][$component])) {
                            $pStyles['borders'][$component] = $pStyles['borders']['allBorders'];
                        }
                    }
                    unset($pStyles['borders']['allBorders']); // not needed any more
                }
                // 'outline' is a shorthand property for 'top', 'right', 'bottom', 'left'
                //        it applies to components that have not been set explicitly
                if (isset($pStyles['borders']['outline'])) {
                    foreach (['top', 'right', 'bottom', 'left'] as $component) {
                        if (!isset($pStyles['borders'][$component])) {
                            $pStyles['borders'][$component] = $pStyles['borders']['outline'];
                        }
                    }
                    unset($pStyles['borders']['outline']); // not needed any more
                }
                // 'inside' is a shorthand property for 'vertical' and 'horizontal'
                //        it applies to components that have not been set explicitly
                if (isset($pStyles['borders']['inside'])) {
                    foreach (['vertical', 'horizontal'] as $component) {
                        if (!isset($pStyles['borders'][$component])) {
                            $pStyles['borders'][$component] = $pStyles['borders']['inside'];
                        }
                    }
                    unset($pStyles['borders']['inside']); // not needed any more
                }
                // width and height characteristics of selection, 1, 2, or 3 (for 3 or more)
                $xMax = min($rangeEnd[0] - $rangeStart[0] + 1, 3);
                $yMax = min($rangeEnd[1] - $rangeStart[1] + 1, 3);

                // loop through up to 3 x 3 = 9 regions
                for ($x = 1; $x <= $xMax; ++$x) {
                    // start column index for region
                    $colStart = ($x == 3) ?
                        Coordinate::stringFromColumnIndex($rangeEnd[0])
                            : Coordinate::stringFromColumnIndex($rangeStart[0] + $x - 1);
                    // end column index for region
                    $colEnd = ($x == 1) ?
                        Coordinate::stringFromColumnIndex($rangeStart[0])
                            : Coordinate::stringFromColumnIndex($rangeEnd[0] - $xMax + $x);

                    for ($y = 1; $y <= $yMax; ++$y) {
                        // which edges are touching the region
                        $edges = [];
                        if ($x == 1) {
                            // are we at left edge
                            $edges[] = 'left';
                        }
                        if ($x == $xMax) {
                            // are we at right edge
                            $edges[] = 'right';
                        }
                        if ($y == 1) {
                            // are we at top edge?
                            $edges[] = 'top';
                        }
                        if ($y == $yMax) {
                            // are we at bottom edge?
                            $edges[] = 'bottom';
                        }

                        // start row index for region
                        $rowStart = ($y == 3) ?
                            $rangeEnd[1] : $rangeStart[1] + $y - 1;

                        // end row index for region
                        $rowEnd = ($y == 1) ?
                            $rangeStart[1] : $rangeEnd[1] - $yMax + $y;

                        // build range for region
                        $range = $colStart . $rowStart . ':' . $colEnd . $rowEnd;

                        // retrieve relevant style array for region
                        $regionStyles = $pStyles;
                        unset($regionStyles['borders']['inside']);

                        // what are the inner edges of the region when looking at the selection
                        $innerEdges = array_diff(['top', 'right', 'bottom', 'left'], $edges);

                        // inner edges that are not touching the region should take the 'inside' border properties if they have been set
                        foreach ($innerEdges as $innerEdge) {
                            switch ($innerEdge) {
                                case 'top':
                                case 'bottom':
                                    // should pick up 'horizontal' border property if set
                                    if (isset($pStyles['borders']['horizontal'])) {
                                        $regionStyles['borders'][$innerEdge] = $pStyles['borders']['horizontal'];
                                    } else {
                                        unset($regionStyles['borders'][$innerEdge]);
                                    }

                                    break;
                                case 'left':
                                case 'right':
                                    // should pick up 'vertical' border property if set
                                    if (isset($pStyles['borders']['vertical'])) {
                                        $regionStyles['borders'][$innerEdge] = $pStyles['borders']['vertical'];
                                    } else {
                                        unset($regionStyles['borders'][$innerEdge]);
                                    }

                                    break;
                            }
                        }

                        // apply region style to region by calling applyFromArray() in simple mode
                        $this->getActiveSheet()->getStyle($range)->applyFromArray($regionStyles, false);
                    }
                }

                // restore initial cell selection range
                $this->getActiveSheet()->getStyle($pRange);

                return $this;
            }

            // SIMPLE MODE:
            // Selection type, inspect
            if (preg_match('/^[A-Z]+1:[A-Z]+1048576$/', $pRange)) {
                $selectionType = 'COLUMN';
            } elseif (preg_match('/^A\d+:XFD\d+$/', $pRange)) {
                $selectionType = 'ROW';
            } else {
                $selectionType = 'CELL';
            }

            // First loop through columns, rows, or cells to find out which styles are affected by this operation
            switch ($selectionType) {
                case 'COLUMN':
                    $oldXfIndexes = [];
                    for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                        $oldXfIndexes[$this->getActiveSheet()->getColumnDimensionByColumn($col)->getXfIndex()] = true;
                    }
                    foreach ($this->getActiveSheet()->getColumnIterator($rangeStart0, $rangeEnd0) as $columnIterator) {
                        $cellIterator = $columnIterator->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(true);
                        foreach ($cellIterator as $columnCell) {
                            $columnCell->getStyle()->applyFromArray($pStyles);
                        }
                    }

                    break;
                case 'ROW':
                    $oldXfIndexes = [];
                    for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                        if ($this->getActiveSheet()->getRowDimension($row)->getXfIndex() == null) {
                            $oldXfIndexes[0] = true; // row without explicit style should be formatted based on default style
                        } else {
                            $oldXfIndexes[$this->getActiveSheet()->getRowDimension($row)->getXfIndex()] = true;
                        }
                    }
                    foreach ($this->getActiveSheet()->getRowIterator((int) $rangeStart[1], (int) $rangeEnd[1]) as $rowIterator) {
                        $cellIterator = $rowIterator->getCellIterator();
                        $cellIterator->setIterateOnlyExistingCells(true);
                        foreach ($cellIterator as $rowCell) {
                            $rowCell->getStyle()->applyFromArray($pStyles);
                        }
                    }

                    break;
                case 'CELL':
                    $oldXfIndexes = [];
                    for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                        for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                            $oldXfIndexes[$this->getActiveSheet()->getCellByColumnAndRow($col, $row)->getXfIndex()] = true;
                        }
                    }

                    break;
            }

            // clone each of the affected styles, apply the style array, and add the new styles to the workbook
            $workbook = $this->getActiveSheet()->getParent();
            foreach ($oldXfIndexes as $oldXfIndex => $dummy) {
                $style = $workbook->getCellXfByIndex($oldXfIndex);
                $newStyle = clone $style;
                $newStyle->applyFromArray($pStyles);

                if ($existingStyle = $workbook->getCellXfByHashCode($newStyle->getHashCode())) {
                    // there is already such cell Xf in our collection
                    $newXfIndexes[$oldXfIndex] = $existingStyle->getIndex();
                } else {
                    // we don't have such a cell Xf, need to add
                    $workbook->addCellXf($newStyle);
                    $newXfIndexes[$oldXfIndex] = $newStyle->getIndex();
                }
            }

            // Loop through columns, rows, or cells again and update the XF index
            switch ($selectionType) {
                case 'COLUMN':
                    for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                        $columnDimension = $this->getActiveSheet()->getColumnDimensionByColumn($col);
                        $oldXfIndex = $columnDimension->getXfIndex();
                        $columnDimension->setXfIndex($newXfIndexes[$oldXfIndex]);
                    }

                    break;
                case 'ROW':
                    for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                        $rowDimension = $this->getActiveSheet()->getRowDimension($row);
                        $oldXfIndex = $rowDimension->getXfIndex() === null ?
                            0 : $rowDimension->getXfIndex(); // row without explicit style should be formatted based on default style
                        $rowDimension->setXfIndex($newXfIndexes[$oldXfIndex]);
                    }

                    break;
                case 'CELL':
                    for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                        for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                            $cell = $this->getActiveSheet()->getCellByColumnAndRow($col, $row);
                            $oldXfIndex = $cell->getXfIndex();
                            $cell->setXfIndex($newXfIndexes[$oldXfIndex]);
                        }
                    }

                    break;
            }
        } else {
            // not a supervisor, just apply the style array directly on style object
            if (isset($pStyles['fill'])) {
                $this->getFill()->applyFromArray($pStyles['fill']);
            }
            if (isset($pStyles['font'])) {
                $this->getFont()->applyFromArray($pStyles['font']);
            }
            if (isset($pStyles['borders'])) {
                $this->getBorders()->applyFromArray($pStyles['borders']);
            }
            if (isset($pStyles['alignment'])) {
                $this->getAlignment()->applyFromArray($pStyles['alignment']);
            }
            if (isset($pStyles['numberFormat'])) {
                $this->getNumberFormat()->applyFromArray($pStyles['numberFormat']);
            }
            if (isset($pStyles['protection'])) {
                $this->getProtection()->applyFromArray($pStyles['protection']);
            }
            if (isset($pStyles['quotePrefix'])) {
                $this->quotePrefix = $pStyles['quotePrefix'];
            }
        }

        return $this;
    }

    /**
     * Get Fill.
     *
     * @return Fill
     */
    public function getFill()
    {
        return $this->fill;
    }

    /**
     * Get Font.
     *
     * @return Font
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Set font.
     *
     * @return $this
     */
    public function setFont(Font $font)
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Get Borders.
     *
     * @return Borders
     */
    public function getBorders()
    {
        return $this->borders;
    }

    /**
     * Get Alignment.
     *
     * @return Alignment
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * Get Number Format.
     *
     * @return NumberFormat
     */
    public function getNumberFormat()
    {
        return $this->numberFormat;
    }

    /**
     * Get Conditional Styles. Only used on supervisor.
     *
     * @return Conditional[]
     */
    public function getConditionalStyles()
    {
        return $this->getActiveSheet()->getConditionalStyles($this->getActiveCell());
    }

    /**
     * Set Conditional Styles. Only used on supervisor.
     *
     * @param Conditional[] $pValue Array of conditional styles
     *
     * @return $this
     */
    public function setConditionalStyles(array $pValue)
    {
        $this->getActiveSheet()->setConditionalStyles($this->getSelectedCells(), $pValue);

        return $this;
    }

    /**
     * Get Protection.
     *
     * @return Protection
     */
    public function getProtection()
    {
        return $this->protection;
    }

    /**
     * Get quote prefix.
     *
     * @return bool
     */
    public function getQuotePrefix()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getQuotePrefix();
        }

        return $this->quotePrefix;
    }

    /**
     * Set quote prefix.
     *
     * @param bool $pValue
     *
     * @return $this
     */
    public function setQuotePrefix($pValue)
    {
        if ($pValue == '') {
            $pValue = false;
        }
        if ($this->isSupervisor) {
            $styleArray = ['quotePrefix' => $pValue];
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->quotePrefix = (bool) $pValue;
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
        return md5(
            $this->fill->getHashCode() .
            $this->font->getHashCode() .
            $this->borders->getHashCode() .
            $this->alignment->getHashCode() .
            $this->numberFormat->getHashCode() .
            $this->protection->getHashCode() .
            ($this->quotePrefix ? 't' : 'f') .
            __CLASS__
        );
    }

    /**
     * Get own index in style collection.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set own index in style collection.
     *
     * @param int $pValue
     */
    public function setIndex($pValue): void
    {
        $this->index = $pValue;
    }

    protected function exportArray1(): array
    {
        $exportedArray = [];
        $this->exportArray2($exportedArray, 'alignment', $this->getAlignment());
        $this->exportArray2($exportedArray, 'borders', $this->getBorders());
        $this->exportArray2($exportedArray, 'fill', $this->getFill());
        $this->exportArray2($exportedArray, 'font', $this->getFont());
        $this->exportArray2($exportedArray, 'numberFormat', $this->getNumberFormat());
        $this->exportArray2($exportedArray, 'protection', $this->getProtection());
        $this->exportArray2($exportedArray, 'quotePrefx', $this->getQuotePrefix());

        return $exportedArray;
    }
}
