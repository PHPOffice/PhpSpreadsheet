<?php

namespace PhpOffice\PhpSpreadsheet\Style;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Style extends Supervisor
{
    /**
     * Font.
     */
    protected Font $font;

    /**
     * Fill.
     */
    protected Fill $fill;

    /**
     * Borders.
     */
    protected Borders $borders;

    /**
     * Alignment.
     */
    protected Alignment $alignment;

    /**
     * Number Format.
     */
    protected NumberFormat $numberFormat;

    /**
     * Protection.
     */
    protected Protection $protection;

    /**
     * Index of style in collection. Only used for real style.
     */
    protected int $index;

    /**
     * Use Quote Prefix when displaying in cell editor. Only used for real style.
     */
    protected bool $quotePrefix = false;

    /**
     * Internal cache for styles
     * Used when applying style on range of cells (column or row) and cleared when
     * all cells in range is styled.
     *
     * PhpSpreadsheet will always minimize the amount of styles used. So cells with
     * same styles will reference the same Style instance. To check if two styles
     * are similar Style::getHashCode() is used. This call is expensive. To minimize
     * the need to call this method we can cache the internal PHP object id of the
     * Style in the range. Style::getHashCode() will then only be called when we
     * encounter a unique style.
     *
     * @see Style::applyFromArray()
     * @see Style::getHashCode()
     *
     * @var null|array<string, array>
     */
    private static ?array $cachedStyles = null;

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
    public function __construct(bool $isSupervisor = false, bool $isConditional = false)
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
     */
    public function getSharedComponent(): self
    {
        $activeSheet = $this->getActiveSheet();
        $selectedCell = Functions::trimSheetFromCellReference($this->getActiveCell()); // e.g. 'A1'

        if ($activeSheet->cellExists($selectedCell)) {
            $xfIndex = $activeSheet->getCell($selectedCell)->getXfIndex();
        } else {
            $xfIndex = 0;
        }

        return $activeSheet->getParentOrThrow()->getCellXfByIndex($xfIndex);
    }

    /**
     * Get parent. Only used for style supervisor.
     */
    public function getParent(): Spreadsheet
    {
        return $this->getActiveSheet()->getParentOrThrow();
    }

    /**
     * Build style array from subcomponents.
     */
    public function getStyleArray(array $array): array
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
     * @param array $styleArray Array containing style information
     * @param bool $advancedBorders advanced mode for setting borders
     *
     * @return $this
     */
    public function applyFromArray(array $styleArray, bool $advancedBorders = true): static
    {
        if ($this->isSupervisor) {
            $pRange = $this->getSelectedCells();

            // Uppercase coordinate and strip any Worksheet reference from the selected range
            $pRange = strtoupper($pRange);
            if (str_contains($pRange, '!')) {
                $pRangeWorksheet = StringHelper::strToUpper(trim(substr($pRange, 0, (int) strrpos($pRange, '!')), "'"));
                if ($pRangeWorksheet !== '' && StringHelper::strToUpper($this->getActiveSheet()->getTitle()) !== $pRangeWorksheet) {
                    throw new Exception('Invalid Worksheet for specified Range');
                }
                $pRange = strtoupper(Functions::trimSheetFromCellReference($pRange));
            }

            // Is it a cell range or a single cell?
            if (!str_contains($pRange, ':')) {
                $rangeA = $pRange;
                $rangeB = $pRange;
            } else {
                [$rangeA, $rangeB] = explode(':', $pRange);
            }

            // Calculate range outer borders
            $rangeStart = Coordinate::coordinateFromString($rangeA);
            $rangeEnd = Coordinate::coordinateFromString($rangeB);
            $rangeStartIndexes = Coordinate::indexesFromString($rangeA);
            $rangeEndIndexes = Coordinate::indexesFromString($rangeB);

            $columnStart = $rangeStart[0];
            $columnEnd = $rangeEnd[0];

            // Make sure we can loop upwards on rows and columns
            if ($rangeStartIndexes[0] > $rangeEndIndexes[0] && $rangeStartIndexes[1] > $rangeEndIndexes[1]) {
                $tmp = $rangeStartIndexes;
                $rangeStartIndexes = $rangeEndIndexes;
                $rangeEndIndexes = $tmp;
            }

            // ADVANCED MODE:
            if ($advancedBorders && isset($styleArray['borders'])) {
                // 'allBorders' is a shorthand property for 'outline' and 'inside' and
                //        it applies to components that have not been set explicitly
                if (isset($styleArray['borders']['allBorders'])) {
                    foreach (['outline', 'inside'] as $component) {
                        if (!isset($styleArray['borders'][$component])) {
                            $styleArray['borders'][$component] = $styleArray['borders']['allBorders'];
                        }
                    }
                    unset($styleArray['borders']['allBorders']); // not needed any more
                }
                // 'outline' is a shorthand property for 'top', 'right', 'bottom', 'left'
                //        it applies to components that have not been set explicitly
                if (isset($styleArray['borders']['outline'])) {
                    foreach (['top', 'right', 'bottom', 'left'] as $component) {
                        if (!isset($styleArray['borders'][$component])) {
                            $styleArray['borders'][$component] = $styleArray['borders']['outline'];
                        }
                    }
                    unset($styleArray['borders']['outline']); // not needed any more
                }
                // 'inside' is a shorthand property for 'vertical' and 'horizontal'
                //        it applies to components that have not been set explicitly
                if (isset($styleArray['borders']['inside'])) {
                    foreach (['vertical', 'horizontal'] as $component) {
                        if (!isset($styleArray['borders'][$component])) {
                            $styleArray['borders'][$component] = $styleArray['borders']['inside'];
                        }
                    }
                    unset($styleArray['borders']['inside']); // not needed any more
                }
                // width and height characteristics of selection, 1, 2, or 3 (for 3 or more)
                $xMax = min($rangeEndIndexes[0] - $rangeStartIndexes[0] + 1, 3);
                $yMax = min($rangeEndIndexes[1] - $rangeStartIndexes[1] + 1, 3);

                // loop through up to 3 x 3 = 9 regions
                for ($x = 1; $x <= $xMax; ++$x) {
                    // start column index for region
                    $colStart = ($x == 3)
                        ? Coordinate::stringFromColumnIndex($rangeEndIndexes[0])
                        : Coordinate::stringFromColumnIndex($rangeStartIndexes[0] + $x - 1);
                    // end column index for region
                    $colEnd = ($x == 1)
                        ? Coordinate::stringFromColumnIndex($rangeStartIndexes[0])
                        : Coordinate::stringFromColumnIndex($rangeEndIndexes[0] - $xMax + $x);

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
                        $rowStart = ($y == 3)
                            ? $rangeEndIndexes[1] : $rangeStartIndexes[1] + $y - 1;

                        // end row index for region
                        $rowEnd = ($y == 1)
                            ? $rangeStartIndexes[1] : $rangeEndIndexes[1] - $yMax + $y;

                        // build range for region
                        $range = $colStart . $rowStart . ':' . $colEnd . $rowEnd;

                        // retrieve relevant style array for region
                        $regionStyles = $styleArray;
                        unset($regionStyles['borders']['inside']);

                        // what are the inner edges of the region when looking at the selection
                        $innerEdges = array_diff(['top', 'right', 'bottom', 'left'], $edges);

                        // inner edges that are not touching the region should take the 'inside' border properties if they have been set
                        foreach ($innerEdges as $innerEdge) {
                            switch ($innerEdge) {
                                case 'top':
                                case 'bottom':
                                    // should pick up 'horizontal' border property if set
                                    if (isset($styleArray['borders']['horizontal'])) {
                                        $regionStyles['borders'][$innerEdge] = $styleArray['borders']['horizontal'];
                                    } else {
                                        unset($regionStyles['borders'][$innerEdge]);
                                    }

                                    break;
                                case 'left':
                                case 'right':
                                    // should pick up 'vertical' border property if set
                                    if (isset($styleArray['borders']['vertical'])) {
                                        $regionStyles['borders'][$innerEdge] = $styleArray['borders']['vertical'];
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

                // Enable caching of styles
                self::$cachedStyles = ['hashByObjId' => [], 'styleByHash' => []];
            } elseif (preg_match('/^A\d+:XFD\d+$/', $pRange)) {
                $selectionType = 'ROW';

                // Enable caching of styles
                self::$cachedStyles = ['hashByObjId' => [], 'styleByHash' => []];
            } else {
                $selectionType = 'CELL';
            }

            // First loop through columns, rows, or cells to find out which styles are affected by this operation
            $oldXfIndexes = $this->getOldXfIndexes($selectionType, $rangeStartIndexes, $rangeEndIndexes, $columnStart, $columnEnd, $styleArray);

            // clone each of the affected styles, apply the style array, and add the new styles to the workbook
            $workbook = $this->getActiveSheet()->getParentOrThrow();
            $newXfIndexes = [];
            foreach ($oldXfIndexes as $oldXfIndex => $dummy) {
                $style = $workbook->getCellXfByIndex($oldXfIndex);

                // $cachedStyles is set when applying style for a range of cells, either column or row
                if (self::$cachedStyles === null) {
                    // Clone the old style and apply style-array
                    $newStyle = clone $style;
                    $newStyle->applyFromArray($styleArray);

                    // Look for existing style we can use instead (reduce memory usage)
                    $existingStyle = $workbook->getCellXfByHashCode($newStyle->getHashCode());
                } else {
                    // Style cache is stored by Style::getHashCode(). But calling this method is
                    // expensive. So we cache the php obj id -> hash.
                    $objId = spl_object_id($style);

                    // Look for the original HashCode
                    $styleHash = self::$cachedStyles['hashByObjId'][$objId] ?? null;
                    if ($styleHash === null) {
                        // This object_id is not cached, store the hashcode in case encounter again
                        $styleHash = self::$cachedStyles['hashByObjId'][$objId] = $style->getHashCode();
                    }

                    // Find existing style by hash.
                    $existingStyle = self::$cachedStyles['styleByHash'][$styleHash] ?? null;

                    if (!$existingStyle) {
                        // The old style combined with the new style array is not cached, so we create it now
                        $newStyle = clone $style;
                        $newStyle->applyFromArray($styleArray);

                        // Look for similar style in workbook to reduce memory usage
                        $existingStyle = $workbook->getCellXfByHashCode($newStyle->getHashCode());

                        // Cache the new style by original hashcode
                        self::$cachedStyles['styleByHash'][$styleHash] = $existingStyle instanceof self ? $existingStyle : $newStyle;
                    }
                }

                if ($existingStyle) {
                    // there is already such cell Xf in our collection
                    $newXfIndexes[$oldXfIndex] = $existingStyle->getIndex();
                } else {
                    if (!isset($newStyle)) {
                        // Handle bug in PHPStan, see https://github.com/phpstan/phpstan/issues/5805
                        // $newStyle should always be defined.
                        // This block might not be needed in the future
                        // @codeCoverageIgnoreStart
                        $newStyle = clone $style;
                        $newStyle->applyFromArray($styleArray);
                        // @codeCoverageIgnoreEnd
                    }

                    // we don't have such a cell Xf, need to add
                    $workbook->addCellXf($newStyle);
                    $newXfIndexes[$oldXfIndex] = $newStyle->getIndex();
                }
            }

            // Loop through columns, rows, or cells again and update the XF index
            switch ($selectionType) {
                case 'COLUMN':
                    for ($col = $rangeStartIndexes[0]; $col <= $rangeEndIndexes[0]; ++$col) {
                        $columnDimension = $this->getActiveSheet()->getColumnDimensionByColumn($col);
                        $oldXfIndex = $columnDimension->getXfIndex();
                        $columnDimension->setXfIndex($newXfIndexes[$oldXfIndex]);
                    }

                    // Disable caching of styles
                    self::$cachedStyles = null;

                    break;
                case 'ROW':
                    for ($row = $rangeStartIndexes[1]; $row <= $rangeEndIndexes[1]; ++$row) {
                        $rowDimension = $this->getActiveSheet()->getRowDimension($row);
                        // row without explicit style should be formatted based on default style
                        $oldXfIndex = $rowDimension->getXfIndex() ?? 0;
                        $rowDimension->setXfIndex($newXfIndexes[$oldXfIndex]);
                    }

                    // Disable caching of styles
                    self::$cachedStyles = null;

                    break;
                case 'CELL':
                    for ($col = $rangeStartIndexes[0]; $col <= $rangeEndIndexes[0]; ++$col) {
                        for ($row = $rangeStartIndexes[1]; $row <= $rangeEndIndexes[1]; ++$row) {
                            $cell = $this->getActiveSheet()->getCell([$col, $row]);
                            $oldXfIndex = $cell->getXfIndex();
                            $cell->setXfIndex($newXfIndexes[$oldXfIndex]);
                        }
                    }

                    break;
            }
        } else {
            // not a supervisor, just apply the style array directly on style object
            if (isset($styleArray['fill'])) {
                $this->getFill()->applyFromArray($styleArray['fill']);
            }
            if (isset($styleArray['font'])) {
                $this->getFont()->applyFromArray($styleArray['font']);
            }
            if (isset($styleArray['borders'])) {
                $this->getBorders()->applyFromArray($styleArray['borders']);
            }
            if (isset($styleArray['alignment'])) {
                $this->getAlignment()->applyFromArray($styleArray['alignment']);
            }
            if (isset($styleArray['numberFormat'])) {
                $this->getNumberFormat()->applyFromArray($styleArray['numberFormat']);
            }
            if (isset($styleArray['protection'])) {
                $this->getProtection()->applyFromArray($styleArray['protection']);
            }
            if (isset($styleArray['quotePrefix'])) {
                $this->quotePrefix = $styleArray['quotePrefix'];
            }
        }

        return $this;
    }

    private function getOldXfIndexes(string $selectionType, array $rangeStart, array $rangeEnd, string $columnStart, string $columnEnd, array $styleArray): array
    {
        $oldXfIndexes = [];
        switch ($selectionType) {
            case 'COLUMN':
                for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                    $oldXfIndexes[$this->getActiveSheet()->getColumnDimensionByColumn($col)->getXfIndex()] = true;
                }
                foreach ($this->getActiveSheet()->getColumnIterator($columnStart, $columnEnd) as $columnIterator) {
                    $cellIterator = $columnIterator->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);
                    foreach ($cellIterator as $columnCell) {
                        if ($columnCell !== null) {
                            $columnCell->getStyle()->applyFromArray($styleArray);
                        }
                    }
                }

                break;
            case 'ROW':
                for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                    if ($this->getActiveSheet()->getRowDimension($row)->getXfIndex() === null) {
                        $oldXfIndexes[0] = true; // row without explicit style should be formatted based on default style
                    } else {
                        $oldXfIndexes[$this->getActiveSheet()->getRowDimension($row)->getXfIndex()] = true;
                    }
                }
                foreach ($this->getActiveSheet()->getRowIterator((int) $rangeStart[1], (int) $rangeEnd[1]) as $rowIterator) {
                    $cellIterator = $rowIterator->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(true);
                    foreach ($cellIterator as $rowCell) {
                        if ($rowCell !== null) {
                            $rowCell->getStyle()->applyFromArray($styleArray);
                        }
                    }
                }

                break;
            case 'CELL':
                for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
                    for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                        $oldXfIndexes[$this->getActiveSheet()->getCell([$col, $row])->getXfIndex()] = true;
                    }
                }

                break;
        }

        return $oldXfIndexes;
    }

    /**
     * Get Fill.
     */
    public function getFill(): Fill
    {
        return $this->fill;
    }

    /**
     * Get Font.
     */
    public function getFont(): Font
    {
        return $this->font;
    }

    /**
     * Set font.
     *
     * @return $this
     */
    public function setFont(Font $font): static
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Get Borders.
     */
    public function getBorders(): Borders
    {
        return $this->borders;
    }

    /**
     * Get Alignment.
     */
    public function getAlignment(): Alignment
    {
        return $this->alignment;
    }

    /**
     * Get Number Format.
     */
    public function getNumberFormat(): NumberFormat
    {
        return $this->numberFormat;
    }

    /**
     * Get Conditional Styles. Only used on supervisor.
     *
     * @return Conditional[]
     */
    public function getConditionalStyles(): array
    {
        return $this->getActiveSheet()->getConditionalStyles($this->getActiveCell());
    }

    /**
     * Set Conditional Styles. Only used on supervisor.
     *
     * @param Conditional[] $conditionalStyleArray Array of conditional styles
     *
     * @return $this
     */
    public function setConditionalStyles(array $conditionalStyleArray): static
    {
        $this->getActiveSheet()->setConditionalStyles($this->getSelectedCells(), $conditionalStyleArray);

        return $this;
    }

    /**
     * Get Protection.
     */
    public function getProtection(): Protection
    {
        return $this->protection;
    }

    /**
     * Get quote prefix.
     */
    public function getQuotePrefix(): bool
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getQuotePrefix();
        }

        return $this->quotePrefix;
    }

    /**
     * Set quote prefix.
     *
     * @return $this
     */
    public function setQuotePrefix(bool $quotePrefix): static
    {
        if ($quotePrefix == '') {
            $quotePrefix = false;
        }
        if ($this->isSupervisor) {
            $styleArray = ['quotePrefix' => $quotePrefix];
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->quotePrefix = (bool) $quotePrefix;
        }

        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode(): string
    {
        return md5(
            $this->fill->getHashCode()
            . $this->font->getHashCode()
            . $this->borders->getHashCode()
            . $this->alignment->getHashCode()
            . $this->numberFormat->getHashCode()
            . $this->protection->getHashCode()
            . ($this->quotePrefix ? 't' : 'f')
            . __CLASS__
        );
    }

    /**
     * Get own index in style collection.
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * Set own index in style collection.
     */
    public function setIndex(int $index): void
    {
        $this->index = $index;
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
