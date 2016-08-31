<?php

namespace PhpOffice\PhpSpreadsheet;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PhpSpreadsheet
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class Style extends Style\Supervisor implements IComparable
{
    /**
     * Font
     *
     * @var Style\Font
     */
    protected $font;

    /**
     * Fill
     *
     * @var Style\Fill
     */
    protected $fill;

    /**
     * Borders
     *
     * @var Style\Borders
     */
    protected $borders;

    /**
     * Alignment
     *
     * @var Style\Alignment
     */
    protected $alignment;

    /**
     * Number Format
     *
     * @var Style\NumberFormat
     */
    protected $numberFormat;

    /**
     * Conditional styles
     *
     * @var Style\Conditional[]
     */
    protected $conditionalStyles;

    /**
     * Protection
     *
     * @var Style\Protection
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
     * Create a new Style
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
        // Supervisor?
        $this->isSupervisor = $isSupervisor;

        // Initialise values
        $this->conditionalStyles = [];
        $this->font = new Style\Font($isSupervisor, $isConditional);
        $this->fill = new Style\Fill($isSupervisor, $isConditional);
        $this->borders = new Style\Borders($isSupervisor, $isConditional);
        $this->alignment = new Style\Alignment($isSupervisor, $isConditional);
        $this->numberFormat = new Style\NumberFormat($isSupervisor, $isConditional);
        $this->protection = new Style\Protection($isSupervisor, $isConditional);

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
     * Only used for style supervisor
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
     * Get parent. Only used for style supervisor
     *
     * @return Spreadsheet
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Build style array from subcomponents
     *
     * @param array $array
     * @return array
     */
    public function getStyleArray($array)
    {
        return ['quotePrefix' => $array];
    }

    /**
     * Apply styles from array
     *
     * <code>
     * $spreadsheet->getActiveSheet()->getStyle('B2')->applyFromArray(
     *         array(
     *             'font'    => array(
     *                 'name'      => 'Arial',
     *                 'bold'      => true,
     *                 'italic'    => false,
     *                 'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE,
     *                 'strike'    => false,
     *                 'color'     => array(
     *                     'rgb' => '808080'
     *                 )
     *             ),
     *             'borders' => array(
     *                 'bottom'     => array(
     *                     'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHDOT,
     *                     'color' => array(
     *                         'rgb' => '808080'
     *                     )
     *                 ),
     *                 'top'     => array(
     *                     'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHDOT,
     *                     'color' => array(
     *                         'rgb' => '808080'
     *                     )
     *                 )
     *             ),
     *             'quotePrefix'    => true
     *         )
     * );
     * </code>
     *
     * @param   array    $pStyles    Array containing style information
     * @param   bool        $pAdvanced    Advanced mode for setting borders.
     * @throws  Exception
     * @return Style
     */
    public function applyFromArray($pStyles = null, $pAdvanced = true)
    {
        if (is_array($pStyles)) {
            if ($this->isSupervisor) {
                $pRange = $this->getSelectedCells();

                // Uppercase coordinate
                $pRange = strtoupper($pRange);

                // Is it a cell range or a single cell?
                if (strpos($pRange, ':') === false) {
                    $rangeA = $pRange;
                    $rangeB = $pRange;
                } else {
                    list($rangeA, $rangeB) = explode(':', $pRange);
                }

                // Calculate range outer borders
                $rangeStart = Cell::coordinateFromString($rangeA);
                $rangeEnd = Cell::coordinateFromString($rangeB);

                // Translate column into index
                $rangeStart[0] = Cell::columnIndexFromString($rangeStart[0]) - 1;
                $rangeEnd[0] = Cell::columnIndexFromString($rangeEnd[0]) - 1;

                // Make sure we can loop upwards on rows and columns
                if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
                    $tmp = $rangeStart;
                    $rangeStart = $rangeEnd;
                    $rangeEnd = $tmp;
                }

                // ADVANCED MODE:
                if ($pAdvanced && isset($pStyles['borders'])) {
                    // 'allborders' is a shorthand property for 'outline' and 'inside' and
                    //        it applies to components that have not been set explicitly
                    if (isset($pStyles['borders']['allborders'])) {
                        foreach (['outline', 'inside'] as $component) {
                            if (!isset($pStyles['borders'][$component])) {
                                $pStyles['borders'][$component] = $pStyles['borders']['allborders'];
                            }
                        }
                        unset($pStyles['borders']['allborders']); // not needed any more
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
                            Cell::stringFromColumnIndex($rangeEnd[0])
                                : Cell::stringFromColumnIndex($rangeStart[0] + $x - 1);
                        // end column index for region
                        $colEnd = ($x == 1) ?
                            Cell::stringFromColumnIndex($rangeStart[0])
                                : Cell::stringFromColumnIndex($rangeEnd[0] - $xMax + $x);

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

                    return $this;
                }

                // SIMPLE MODE:
                // Selection type, inspect
                if (preg_match('/^[A-Z]+1:[A-Z]+1048576$/', $pRange)) {
                    $selectionType = 'COLUMN';
                } elseif (preg_match('/^A[0-9]+:XFD[0-9]+$/', $pRange)) {
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
                if (array_key_exists('fill', $pStyles)) {
                    $this->getFill()->applyFromArray($pStyles['fill']);
                }
                if (array_key_exists('font', $pStyles)) {
                    $this->getFont()->applyFromArray($pStyles['font']);
                }
                if (array_key_exists('borders', $pStyles)) {
                    $this->getBorders()->applyFromArray($pStyles['borders']);
                }
                if (array_key_exists('alignment', $pStyles)) {
                    $this->getAlignment()->applyFromArray($pStyles['alignment']);
                }
                if (array_key_exists('numberformat', $pStyles)) {
                    $this->getNumberFormat()->applyFromArray($pStyles['numberformat']);
                }
                if (array_key_exists('protection', $pStyles)) {
                    $this->getProtection()->applyFromArray($pStyles['protection']);
                }
                if (array_key_exists('quotePrefix', $pStyles)) {
                    $this->quotePrefix = $pStyles['quotePrefix'];
                }
            }
        } else {
            throw new Exception('Invalid style array passed.');
        }

        return $this;
    }

    /**
     * Get Fill
     *
     * @return Style\Fill
     */
    public function getFill()
    {
        return $this->fill;
    }

    /**
     * Get Font
     *
     * @return Style\Font
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Set font
     *
     * @param Style\Font $font
     * @return Style
     */
    public function setFont(Style\Font $font)
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Get Borders
     *
     * @return Style\Borders
     */
    public function getBorders()
    {
        return $this->borders;
    }

    /**
     * Get Alignment
     *
     * @return Style\Alignment
     */
    public function getAlignment()
    {
        return $this->alignment;
    }

    /**
     * Get Number Format
     *
     * @return Style\NumberFormat
     */
    public function getNumberFormat()
    {
        return $this->numberFormat;
    }

    /**
     * Get Conditional Styles. Only used on supervisor.
     *
     * @return Style\Conditional[]
     */
    public function getConditionalStyles()
    {
        return $this->getActiveSheet()->getConditionalStyles($this->getActiveCell());
    }

    /**
     * Set Conditional Styles. Only used on supervisor.
     *
     * @param Style\Conditional[] $pValue Array of condtional styles
     * @return Style
     */
    public function setConditionalStyles($pValue = null)
    {
        if (is_array($pValue)) {
            $this->getActiveSheet()->setConditionalStyles($this->getSelectedCells(), $pValue);
        }

        return $this;
    }

    /**
     * Get Protection
     *
     * @return Style\Protection
     */
    public function getProtection()
    {
        return $this->protection;
    }

    /**
     * Get quote prefix
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
     * Set quote prefix
     *
     * @param bool $pValue
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
            $this->quotePrefix = (boolean) $pValue;
        }

        return $this;
    }

    /**
     * Get hash code
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        $hashConditionals = '';
        foreach ($this->conditionalStyles as $conditional) {
            $hashConditionals .= $conditional->getHashCode();
        }

        return md5(
            $this->fill->getHashCode() .
            $this->font->getHashCode() .
            $this->borders->getHashCode() .
            $this->alignment->getHashCode() .
            $this->numberFormat->getHashCode() .
            $hashConditionals .
            $this->protection->getHashCode() .
            ($this->quotePrefix ? 't' : 'f') .
            __CLASS__
        );
    }

    /**
     * Get own index in style collection
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set own index in style collection
     *
     * @param int $pValue
     */
    public function setIndex($pValue)
    {
        $this->index = $pValue;
    }
}
