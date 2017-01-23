<?php

namespace PhpOffice\PhpSpreadsheet;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class Worksheet implements IComparable
{
    /* Break types */
    const BREAK_NONE = 0;
    const BREAK_ROW = 1;
    const BREAK_COLUMN = 2;

    /* Sheet state */
    const SHEETSTATE_VISIBLE = 'visible';
    const SHEETSTATE_HIDDEN = 'hidden';
    const SHEETSTATE_VERYHIDDEN = 'veryHidden';

    /**
     * Invalid characters in sheet title.
     *
     * @var array
     */
    private static $invalidCharacters = ['*', ':', '/', '\\', '?', '[', ']'];

    /**
     * Parent spreadsheet.
     *
     * @var Spreadsheet
     */
    private $parent;

    /**
     * Cacheable collection of cells.
     *
     * @var CachedObjectStorage_xxx
     */
    private $cellCollection;

    /**
     * Collection of row dimensions.
     *
     * @var Worksheet\RowDimension[]
     */
    private $rowDimensions = [];

    /**
     * Default row dimension.
     *
     * @var Worksheet\RowDimension
     */
    private $defaultRowDimension;

    /**
     * Collection of column dimensions.
     *
     * @var Worksheet\ColumnDimension[]
     */
    private $columnDimensions = [];

    /**
     * Default column dimension.
     *
     * @var Worksheet\ColumnDimension
     */
    private $defaultColumnDimension = null;

    /**
     * Collection of drawings.
     *
     * @var Worksheet\BaseDrawing[]
     */
    private $drawingCollection = null;

    /**
     * Collection of Chart objects.
     *
     * @var Chart[]
     */
    private $chartCollection = [];

    /**
     * Worksheet title.
     *
     * @var string
     */
    private $title;

    /**
     * Sheet state.
     *
     * @var string
     */
    private $sheetState;

    /**
     * Page setup.
     *
     * @var Worksheet\PageSetup
     */
    private $pageSetup;

    /**
     * Page margins.
     *
     * @var Worksheet\PageMargins
     */
    private $pageMargins;

    /**
     * Page header/footer.
     *
     * @var Worksheet\HeaderFooter
     */
    private $headerFooter;

    /**
     * Sheet view.
     *
     * @var Worksheet\SheetView
     */
    private $sheetView;

    /**
     * Protection.
     *
     * @var Worksheet\Protection
     */
    private $protection;

    /**
     * Collection of styles.
     *
     * @var Style[]
     */
    private $styles = [];

    /**
     * Conditional styles. Indexed by cell coordinate, e.g. 'A1'.
     *
     * @var array
     */
    private $conditionalStylesCollection = [];

    /**
     * Is the current cell collection sorted already?
     *
     * @var bool
     */
    private $cellCollectionIsSorted = false;

    /**
     * Collection of breaks.
     *
     * @var array
     */
    private $breaks = [];

    /**
     * Collection of merged cell ranges.
     *
     * @var array
     */
    private $mergeCells = [];

    /**
     * Collection of protected cell ranges.
     *
     * @var array
     */
    private $protectedCells = [];

    /**
     * Autofilter Range and selection.
     *
     * @var Worksheet\AutoFilter
     */
    private $autoFilter;

    /**
     * Freeze pane.
     *
     * @var string
     */
    private $freezePane = '';

    /**
     * Show gridlines?
     *
     * @var bool
     */
    private $showGridlines = true;

    /**
     * Print gridlines?
     *
     * @var bool
     */
    private $printGridlines = false;

    /**
     * Show row and column headers?
     *
     * @var bool
     */
    private $showRowColHeaders = true;

    /**
     * Show summary below? (Row/Column outline).
     *
     * @var bool
     */
    private $showSummaryBelow = true;

    /**
     * Show summary right? (Row/Column outline).
     *
     * @var bool
     */
    private $showSummaryRight = true;

    /**
     * Collection of comments.
     *
     * @var Comment[]
     */
    private $comments = [];

    /**
     * Active cell. (Only one!).
     *
     * @var string
     */
    private $activeCell = 'A1';

    /**
     * Selected cells.
     *
     * @var string
     */
    private $selectedCells = 'A1';

    /**
     * Cached highest column.
     *
     * @var string
     */
    private $cachedHighestColumn = 'A';

    /**
     * Cached highest row.
     *
     * @var int
     */
    private $cachedHighestRow = 1;

    /**
     * Right-to-left?
     *
     * @var bool
     */
    private $rightToLeft = false;

    /**
     * Hyperlinks. Indexed by cell coordinate, e.g. 'A1'.
     *
     * @var array
     */
    private $hyperlinkCollection = [];

    /**
     * Data validation objects. Indexed by cell coordinate, e.g. 'A1'.
     *
     * @var array
     */
    private $dataValidationCollection = [];

    /**
     * Tab color.
     *
     * @var Style\Color
     */
    private $tabColor;

    /**
     * Dirty flag.
     *
     * @var bool
     */
    private $dirty = true;

    /**
     * Hash.
     *
     * @var string
     */
    private $hash;

    /**
     * CodeName.
     *
     * @var string
     */
    private $codeName = null;

    /**
     * Create a new worksheet.
     *
     * @param Spreadsheet $parent
     * @param string $pTitle
     */
    public function __construct(Spreadsheet $parent = null, $pTitle = 'Worksheet')
    {
        // Set parent and title
        $this->parent = $parent;
        $this->setTitle($pTitle, false);
        // setTitle can change $pTitle
        $this->setCodeName($this->getTitle());
        $this->setSheetState(self::SHEETSTATE_VISIBLE);

        $this->cellCollection = CachedObjectStorageFactory::getInstance($this);
        // Set page setup
        $this->pageSetup = new Worksheet\PageSetup();
        // Set page margins
        $this->pageMargins = new Worksheet\PageMargins();
        // Set page header/footer
        $this->headerFooter = new Worksheet\HeaderFooter();
        // Set sheet view
        $this->sheetView = new Worksheet\SheetView();
        // Drawing collection
        $this->drawingCollection = new \ArrayObject();
        // Chart collection
        $this->chartCollection = new \ArrayObject();
        // Protection
        $this->protection = new Worksheet\Protection();
        // Default row dimension
        $this->defaultRowDimension = new Worksheet\RowDimension(null);
        // Default column dimension
        $this->defaultColumnDimension = new Worksheet\ColumnDimension(null);
        $this->autoFilter = new Worksheet\AutoFilter(null, $this);
    }

    /**
     * Disconnect all cells from this Worksheet object,
     * typically so that the worksheet object can be unset.
     */
    public function disconnectCells()
    {
        if ($this->cellCollection !== null) {
            $this->cellCollection->unsetWorksheetCells();
            $this->cellCollection = null;
        }
        //    detach ourself from the workbook, so that it can then delete this worksheet successfully
        $this->parent = null;
    }

    /**
     * Code to execute when this worksheet is unset().
     */
    public function __destruct()
    {
        Calculation::getInstance($this->parent)->clearCalculationCacheForWorksheet($this->title);

        $this->disconnectCells();
    }

    /**
     * Return the cache controller for the cell collection.
     *
     * @return CachedObjectStorage_xxx
     */
    public function getCellCacheController()
    {
        return $this->cellCollection;
    }

    /**
     * Get array of invalid characters for sheet title.
     *
     * @return array
     */
    public static function getInvalidCharacters()
    {
        return self::$invalidCharacters;
    }

    /**
     * Check sheet code name for valid Excel syntax.
     *
     * @param string $pValue The string to check
     *
     * @throws Exception
     *
     * @return string The valid string
     */
    private static function checkSheetCodeName($pValue)
    {
        $CharCount = Shared\StringHelper::countCharacters($pValue);
        if ($CharCount == 0) {
            throw new Exception('Sheet code name cannot be empty.');
        }
        // Some of the printable ASCII characters are invalid:  * : / \ ? [ ] and  first and last characters cannot be a "'"
        if ((str_replace(self::$invalidCharacters, '', $pValue) !== $pValue) ||
            (Shared\StringHelper::substring($pValue, -1, 1) == '\'') ||
            (Shared\StringHelper::substring($pValue, 0, 1) == '\'')) {
            throw new Exception('Invalid character found in sheet code name');
        }

        // Maximum 31 characters allowed for sheet title
        if ($CharCount > 31) {
            throw new Exception('Maximum 31 characters allowed in sheet code name.');
        }

        return $pValue;
    }

    /**
     * Check sheet title for valid Excel syntax.
     *
     * @param string $pValue The string to check
     *
     * @throws Exception
     *
     * @return string The valid string
     */
    private static function checkSheetTitle($pValue)
    {
        // Some of the printable ASCII characters are invalid:  * : / \ ? [ ]
        if (str_replace(self::$invalidCharacters, '', $pValue) !== $pValue) {
            throw new Exception('Invalid character found in sheet title');
        }

        // Maximum 31 characters allowed for sheet title
        if (Shared\StringHelper::countCharacters($pValue) > 31) {
            throw new Exception('Maximum 31 characters allowed in sheet title.');
        }

        return $pValue;
    }

    /**
     * Get collection of cells.
     *
     * @param bool $pSorted Also sort the cell collection?
     *
     * @return Cell[]
     */
    public function getCellCollection($pSorted = true)
    {
        if ($pSorted) {
            // Re-order cell collection
            return $this->sortCellCollection();
        }
        if ($this->cellCollection !== null) {
            return $this->cellCollection->getCellList();
        }

        return [];
    }

    /**
     * Sort collection of cells.
     *
     * @return Worksheet
     */
    public function sortCellCollection()
    {
        if ($this->cellCollection !== null) {
            return $this->cellCollection->getSortedCellList();
        }

        return [];
    }

    /**
     * Get collection of row dimensions.
     *
     * @return Worksheet\RowDimension[]
     */
    public function getRowDimensions()
    {
        return $this->rowDimensions;
    }

    /**
     * Get default row dimension.
     *
     * @return Worksheet\RowDimension
     */
    public function getDefaultRowDimension()
    {
        return $this->defaultRowDimension;
    }

    /**
     * Get collection of column dimensions.
     *
     * @return Worksheet\ColumnDimension[]
     */
    public function getColumnDimensions()
    {
        return $this->columnDimensions;
    }

    /**
     * Get default column dimension.
     *
     * @return Worksheet\ColumnDimension
     */
    public function getDefaultColumnDimension()
    {
        return $this->defaultColumnDimension;
    }

    /**
     * Get collection of drawings.
     *
     * @return Worksheet\BaseDrawing[]
     */
    public function getDrawingCollection()
    {
        return $this->drawingCollection;
    }

    /**
     * Get collection of charts.
     *
     * @return Chart[]
     */
    public function getChartCollection()
    {
        return $this->chartCollection;
    }

    /**
     * Add chart.
     *
     * @param Chart $pChart
     * @param int|null $iChartIndex Index where chart should go (0,1,..., or null for last)
     *
     * @return Chart
     */
    public function addChart(Chart $pChart = null, $iChartIndex = null)
    {
        $pChart->setWorksheet($this);
        if (is_null($iChartIndex)) {
            $this->chartCollection[] = $pChart;
        } else {
            // Insert the chart at the requested index
            array_splice($this->chartCollection, $iChartIndex, 0, [$pChart]);
        }

        return $pChart;
    }

    /**
     * Return the count of charts on this worksheet.
     *
     * @return int The number of charts
     */
    public function getChartCount()
    {
        return count($this->chartCollection);
    }

    /**
     * Get a chart by its index position.
     *
     * @param string $index Chart index position
     *
     * @throws Exception
     *
     * @return false|Chart
     */
    public function getChartByIndex($index = null)
    {
        $chartCount = count($this->chartCollection);
        if ($chartCount == 0) {
            return false;
        }
        if (is_null($index)) {
            $index = --$chartCount;
        }
        if (!isset($this->chartCollection[$index])) {
            return false;
        }

        return $this->chartCollection[$index];
    }

    /**
     * Return an array of the names of charts on this worksheet.
     *
     * @throws Exception
     *
     * @return string[] The names of charts
     */
    public function getChartNames()
    {
        $chartNames = [];
        foreach ($this->chartCollection as $chart) {
            $chartNames[] = $chart->getName();
        }

        return $chartNames;
    }

    /**
     * Get a chart by name.
     *
     * @param string $chartName Chart name
     *
     * @throws Exception
     *
     * @return false|Chart
     */
    public function getChartByName($chartName = '')
    {
        $chartCount = count($this->chartCollection);
        if ($chartCount == 0) {
            return false;
        }
        foreach ($this->chartCollection as $index => $chart) {
            if ($chart->getName() == $chartName) {
                return $this->chartCollection[$index];
            }
        }

        return false;
    }

    /**
     * Refresh column dimensions.
     *
     * @return Worksheet
     */
    public function refreshColumnDimensions()
    {
        $currentColumnDimensions = $this->getColumnDimensions();
        $newColumnDimensions = [];

        foreach ($currentColumnDimensions as $objColumnDimension) {
            $newColumnDimensions[$objColumnDimension->getColumnIndex()] = $objColumnDimension;
        }

        $this->columnDimensions = $newColumnDimensions;

        return $this;
    }

    /**
     * Refresh row dimensions.
     *
     * @return Worksheet
     */
    public function refreshRowDimensions()
    {
        $currentRowDimensions = $this->getRowDimensions();
        $newRowDimensions = [];

        foreach ($currentRowDimensions as $objRowDimension) {
            $newRowDimensions[$objRowDimension->getRowIndex()] = $objRowDimension;
        }

        $this->rowDimensions = $newRowDimensions;

        return $this;
    }

    /**
     * Calculate worksheet dimension.
     *
     * @return string String containing the dimension of this worksheet
     */
    public function calculateWorksheetDimension()
    {
        // Return
        return 'A1' . ':' . $this->getHighestColumn() . $this->getHighestRow();
    }

    /**
     * Calculate worksheet data dimension.
     *
     * @return string String containing the dimension of this worksheet that actually contain data
     */
    public function calculateWorksheetDataDimension()
    {
        // Return
        return 'A1' . ':' . $this->getHighestDataColumn() . $this->getHighestDataRow();
    }

    /**
     * Calculate widths for auto-size columns.
     *
     * @return Worksheet;
     */
    public function calculateColumnWidths()
    {
        // initialize $autoSizes array
        $autoSizes = [];
        foreach ($this->getColumnDimensions() as $colDimension) {
            if ($colDimension->getAutoSize()) {
                $autoSizes[$colDimension->getColumnIndex()] = -1;
            }
        }

        // There is only something to do if there are some auto-size columns
        if (!empty($autoSizes)) {
            // build list of cells references that participate in a merge
            $isMergeCell = [];
            foreach ($this->getMergeCells() as $cells) {
                foreach (Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
                    $isMergeCell[$cellReference] = true;
                }
            }

            // loop through all cells in the worksheet
            foreach ($this->getCellCollection(false) as $cellID) {
                $cell = $this->getCell($cellID, false);
                if ($cell !== null && isset($autoSizes[$this->cellCollection->getCurrentColumn()])) {
                    //Determine if cell is in merge range
                    $isMerged = isset($isMergeCell[$this->cellCollection->getCurrentAddress()]);

                    //By default merged cells should be ignored
                    $isMergedButProceed = false;

                    //The only exception is if it's a merge range value cell of a 'vertical' randge (1 column wide)
                    if ($isMerged && $cell->isMergeRangeValueCell()) {
                        $range = $cell->getMergeRange();
                        $rangeBoundaries = Cell::rangeDimension($range);
                        if ($rangeBoundaries[0] == 1) {
                            $isMergedButProceed = true;
                        }
                    }

                    // Determine width if cell does not participate in a merge or does and is a value cell of 1-column wide range
                    if (!$isMerged || $isMergedButProceed) {
                        // Calculated value
                        // To formatted string
                        $cellValue = Style\NumberFormat::toFormattedString(
                            $cell->getCalculatedValue(),
                            $this->getParent()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode()
                        );

                        $autoSizes[$this->cellCollection->getCurrentColumn()] = max(
                            (float) $autoSizes[$this->cellCollection->getCurrentColumn()],
                            (float) Shared\Font::calculateColumnWidth(
                                $this->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont(),
                                $cellValue,
                                $this->getParent()->getCellXfByIndex($cell->getXfIndex())->getAlignment()->getTextRotation(),
                                $this->getParent()->getDefaultStyle()->getFont()
                            )
                        );
                    }
                }
            }

            // adjust column widths
            foreach ($autoSizes as $columnIndex => $width) {
                if ($width == -1) {
                    $width = $this->getDefaultColumnDimension()->getWidth();
                }
                $this->getColumnDimension($columnIndex)->setWidth($width);
            }
        }

        return $this;
    }

    /**
     * Get parent.
     *
     * @return Spreadsheet
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Re-bind parent.
     *
     * @param Spreadsheet $parent
     *
     * @return Worksheet
     */
    public function rebindParent(Spreadsheet $parent)
    {
        if ($this->parent !== null) {
            $namedRanges = $this->parent->getNamedRanges();
            foreach ($namedRanges as $namedRange) {
                $parent->addNamedRange($namedRange);
            }

            $this->parent->removeSheetByIndex(
                $this->parent->getIndex($this)
            );
        }
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title.
     *
     * @param string $pValue String containing the dimension of this worksheet
     * @param string $updateFormulaCellReferences boolean Flag indicating whether cell references in formulae should
     *            be updated to reflect the new sheet name.
     *          This should be left as the default true, unless you are
     *          certain that no formula cells on any worksheet contain
     *          references to this worksheet
     *
     * @return Worksheet
     */
    public function setTitle($pValue = 'Worksheet', $updateFormulaCellReferences = true)
    {
        // Is this a 'rename' or not?
        if ($this->getTitle() == $pValue) {
            return $this;
        }

        // Syntax check
        self::checkSheetTitle($pValue);

        // Old title
        $oldTitle = $this->getTitle();

        if ($this->parent) {
            // Is there already such sheet name?
            if ($this->parent->sheetNameExists($pValue)) {
                // Use name, but append with lowest possible integer

                if (Shared\StringHelper::countCharacters($pValue) > 29) {
                    $pValue = Shared\StringHelper::substring($pValue, 0, 29);
                }
                $i = 1;
                while ($this->parent->sheetNameExists($pValue . ' ' . $i)) {
                    ++$i;
                    if ($i == 10) {
                        if (Shared\StringHelper::countCharacters($pValue) > 28) {
                            $pValue = Shared\StringHelper::substring($pValue, 0, 28);
                        }
                    } elseif ($i == 100) {
                        if (Shared\StringHelper::countCharacters($pValue) > 27) {
                            $pValue = Shared\StringHelper::substring($pValue, 0, 27);
                        }
                    }
                }

                $altTitle = $pValue . ' ' . $i;

                return $this->setTitle($altTitle, $updateFormulaCellReferences);
            }
        }

        // Set title
        $this->title = $pValue;
        $this->dirty = true;

        if ($this->parent && $this->parent->getCalculationEngine()) {
            // New title
            $newTitle = $this->getTitle();
            $this->parent->getCalculationEngine()
                ->renameCalculationCacheForWorksheet($oldTitle, $newTitle);
            if ($updateFormulaCellReferences) {
                ReferenceHelper::getInstance()->updateNamedFormulas($this->parent, $oldTitle, $newTitle);
            }
        }

        return $this;
    }

    /**
     * Get sheet state.
     *
     * @return string Sheet state (visible, hidden, veryHidden)
     */
    public function getSheetState()
    {
        return $this->sheetState;
    }

    /**
     * Set sheet state.
     *
     * @param string $value Sheet state (visible, hidden, veryHidden)
     *
     * @return Worksheet
     */
    public function setSheetState($value = self::SHEETSTATE_VISIBLE)
    {
        $this->sheetState = $value;

        return $this;
    }

    /**
     * Get page setup.
     *
     * @return Worksheet\PageSetup
     */
    public function getPageSetup()
    {
        return $this->pageSetup;
    }

    /**
     * Set page setup.
     *
     * @param Worksheet\PageSetup $pValue
     *
     * @return Worksheet
     */
    public function setPageSetup(Worksheet\PageSetup $pValue)
    {
        $this->pageSetup = $pValue;

        return $this;
    }

    /**
     * Get page margins.
     *
     * @return Worksheet\PageMargins
     */
    public function getPageMargins()
    {
        return $this->pageMargins;
    }

    /**
     * Set page margins.
     *
     * @param Worksheet\PageMargins $pValue
     *
     * @return Worksheet
     */
    public function setPageMargins(Worksheet\PageMargins $pValue)
    {
        $this->pageMargins = $pValue;

        return $this;
    }

    /**
     * Get page header/footer.
     *
     * @return Worksheet\HeaderFooter
     */
    public function getHeaderFooter()
    {
        return $this->headerFooter;
    }

    /**
     * Set page header/footer.
     *
     * @param Worksheet\HeaderFooter $pValue
     *
     * @return Worksheet
     */
    public function setHeaderFooter(Worksheet\HeaderFooter $pValue)
    {
        $this->headerFooter = $pValue;

        return $this;
    }

    /**
     * Get sheet view.
     *
     * @return Worksheet\SheetView
     */
    public function getSheetView()
    {
        return $this->sheetView;
    }

    /**
     * Set sheet view.
     *
     * @param Worksheet\SheetView $pValue
     *
     * @return Worksheet
     */
    public function setSheetView(Worksheet\SheetView $pValue)
    {
        $this->sheetView = $pValue;

        return $this;
    }

    /**
     * Get Protection.
     *
     * @return Worksheet\Protection
     */
    public function getProtection()
    {
        return $this->protection;
    }

    /**
     * Set Protection.
     *
     * @param Worksheet\Protection $pValue
     *
     * @return Worksheet
     */
    public function setProtection(Worksheet\Protection $pValue)
    {
        $this->protection = $pValue;
        $this->dirty = true;

        return $this;
    }

    /**
     * Get highest worksheet column.
     *
     * @param string $row Return the data highest column for the specified row,
     *                                     or the highest column of any row if no row number is passed
     *
     * @return string Highest column name
     */
    public function getHighestColumn($row = null)
    {
        if ($row == null) {
            return $this->cachedHighestColumn;
        }

        return $this->getHighestDataColumn($row);
    }

    /**
     * Get highest worksheet column that contains data.
     *
     * @param string $row Return the highest data column for the specified row,
     *                                     or the highest data column of any row if no row number is passed
     *
     * @return string Highest column name that contains data
     */
    public function getHighestDataColumn($row = null)
    {
        return $this->cellCollection->getHighestColumn($row);
    }

    /**
     * Get highest worksheet row.
     *
     * @param string $column Return the highest data row for the specified column,
     *                                     or the highest row of any column if no column letter is passed
     *
     * @return int Highest row number
     */
    public function getHighestRow($column = null)
    {
        if ($column == null) {
            return $this->cachedHighestRow;
        }

        return $this->getHighestDataRow($column);
    }

    /**
     * Get highest worksheet row that contains data.
     *
     * @param string $column Return the highest data row for the specified column,
     *                                     or the highest data row of any column if no column letter is passed
     *
     * @return string Highest row number that contains data
     */
    public function getHighestDataRow($column = null)
    {
        return $this->cellCollection->getHighestRow($column);
    }

    /**
     * Get highest worksheet column and highest row that have cell records.
     *
     * @return array Highest column name and highest row number
     */
    public function getHighestRowAndColumn()
    {
        return $this->cellCollection->getHighestRowAndColumn();
    }

    /**
     * Set a cell value.
     *
     * @param string $pCoordinate Coordinate of the cell
     * @param mixed $pValue Value of the cell
     * @param bool $returnCell Return the worksheet (false, default) or the cell (true)
     *
     * @return Worksheet|Cell Depending on the last parameter being specified
     */
    public function setCellValue($pCoordinate = 'A1', $pValue = null, $returnCell = false)
    {
        $cell = $this->getCell(strtoupper($pCoordinate))->setValue($pValue);

        return ($returnCell) ? $cell : $this;
    }

    /**
     * Set a cell value by using numeric cell coordinates.
     *
     * @param int $pColumn Numeric column coordinate of the cell (A = 0)
     * @param int $pRow Numeric row coordinate of the cell
     * @param mixed $pValue Value of the cell
     * @param bool $returnCell Return the worksheet (false, default) or the cell (true)
     *
     * @return Worksheet|Cell Depending on the last parameter being specified
     */
    public function setCellValueByColumnAndRow($pColumn = 0, $pRow = 1, $pValue = null, $returnCell = false)
    {
        $cell = $this->getCellByColumnAndRow($pColumn, $pRow)->setValue($pValue);

        return ($returnCell) ? $cell : $this;
    }

    /**
     * Set a cell value.
     *
     * @param string $pCoordinate Coordinate of the cell
     * @param mixed $pValue Value of the cell
     * @param string $pDataType Explicit data type
     * @param bool $returnCell Return the worksheet (false, default) or the cell (true)
     *
     * @return Worksheet|Cell Depending on the last parameter being specified
     */
    public function setCellValueExplicit($pCoordinate = 'A1', $pValue = null, $pDataType = Cell\DataType::TYPE_STRING, $returnCell = false)
    {
        // Set value
        $cell = $this->getCell(strtoupper($pCoordinate))->setValueExplicit($pValue, $pDataType);

        return ($returnCell) ? $cell : $this;
    }

    /**
     * Set a cell value by using numeric cell coordinates.
     *
     * @param int $pColumn Numeric column coordinate of the cell
     * @param int $pRow Numeric row coordinate of the cell
     * @param mixed $pValue Value of the cell
     * @param string $pDataType Explicit data type
     * @param bool $returnCell Return the worksheet (false, default) or the cell (true)
     *
     * @return Worksheet|Cell Depending on the last parameter being specified
     */
    public function setCellValueExplicitByColumnAndRow($pColumn = 0, $pRow = 1, $pValue = null, $pDataType = Cell\DataType::TYPE_STRING, $returnCell = false)
    {
        $cell = $this->getCellByColumnAndRow($pColumn, $pRow)->setValueExplicit($pValue, $pDataType);

        return ($returnCell) ? $cell : $this;
    }

    /**
     * Get cell at a specific coordinate.
     *
     * @param string $pCoordinate Coordinate of the cell
     * @param bool $createIfNotExists Flag indicating whether a new cell should be created if it doesn't
     *                                       already exist, or a null should be returned instead
     *
     * @throws Exception
     *
     * @return null|Cell Cell that was found/created or null
     */
    public function getCell($pCoordinate = 'A1', $createIfNotExists = true)
    {
        // Check cell collection
        if ($this->cellCollection->isDataSet(strtoupper($pCoordinate))) {
            return $this->cellCollection->getCacheData($pCoordinate);
        }

        // Worksheet reference?
        if (strpos($pCoordinate, '!') !== false) {
            $worksheetReference = self::extractSheetTitle($pCoordinate, true);

            return $this->parent->getSheetByName($worksheetReference[0])->getCell(strtoupper($worksheetReference[1]), $createIfNotExists);
        }

        // Named range?
        if ((!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $pCoordinate, $matches)) &&
            (preg_match('/^' . Calculation::CALCULATION_REGEXP_NAMEDRANGE . '$/i', $pCoordinate, $matches))) {
            $namedRange = NamedRange::resolveRange($pCoordinate, $this);
            if ($namedRange !== null) {
                $pCoordinate = $namedRange->getRange();

                return $namedRange->getWorksheet()->getCell($pCoordinate, $createIfNotExists);
            }
        }

        // Uppercase coordinate
        $pCoordinate = strtoupper($pCoordinate);

        if (strpos($pCoordinate, ':') !== false || strpos($pCoordinate, ',') !== false) {
            throw new Exception('Cell coordinate can not be a range of cells.');
        } elseif (strpos($pCoordinate, '$') !== false) {
            throw new Exception('Cell coordinate must not be absolute.');
        }

        // Create new cell object, if required
        return $createIfNotExists ? $this->createNewCell($pCoordinate) : null;
    }

    /**
     * Get cell at a specific coordinate by using numeric cell coordinates.
     *
     * @param string $pColumn Numeric column coordinate of the cell
     * @param string $pRow Numeric row coordinate of the cell
     * @param bool $createIfNotExists Flag indicating whether a new cell should be created if it doesn't
     *                                       already exist, or a null should be returned instead
     *
     * @return null|Cell Cell that was found/created or null
     */
    public function getCellByColumnAndRow($pColumn = 0, $pRow = 1, $createIfNotExists = true)
    {
        $columnLetter = Cell::stringFromColumnIndex($pColumn);
        $coordinate = $columnLetter . $pRow;

        if ($this->cellCollection->isDataSet($coordinate)) {
            return $this->cellCollection->getCacheData($coordinate);
        }

        // Create new cell object, if required
        return $createIfNotExists ? $this->createNewCell($coordinate) : null;
    }

    /**
     * Create a new cell at the specified coordinate.
     *
     * @param string $pCoordinate Coordinate of the cell
     *
     * @return Cell Cell that was created
     */
    private function createNewCell($pCoordinate)
    {
        $cell = $this->cellCollection->addCacheData(
            $pCoordinate,
            new Cell(null, Cell\DataType::TYPE_NULL, $this)
        );
        $this->cellCollectionIsSorted = false;

        // Coordinates
        $aCoordinates = Cell::coordinateFromString($pCoordinate);
        if (Cell::columnIndexFromString($this->cachedHighestColumn) < Cell::columnIndexFromString($aCoordinates[0])) {
            $this->cachedHighestColumn = $aCoordinates[0];
        }
        $this->cachedHighestRow = max($this->cachedHighestRow, $aCoordinates[1]);

        // Cell needs appropriate xfIndex from dimensions records
        //    but don't create dimension records if they don't already exist
        $rowDimension = $this->getRowDimension($aCoordinates[1], false);
        $columnDimension = $this->getColumnDimension($aCoordinates[0], false);

        if ($rowDimension !== null && $rowDimension->getXfIndex() > 0) {
            // then there is a row dimension with explicit style, assign it to the cell
            $cell->setXfIndex($rowDimension->getXfIndex());
        } elseif ($columnDimension !== null && $columnDimension->getXfIndex() > 0) {
            // then there is a column dimension, assign it to the cell
            $cell->setXfIndex($columnDimension->getXfIndex());
        }

        return $cell;
    }

    /**
     * Does the cell at a specific coordinate exist?
     *
     * @param string $pCoordinate Coordinate of the cell
     *
     * @throws Exception
     *
     * @return bool
     */
    public function cellExists($pCoordinate = 'A1')
    {
        // Worksheet reference?
        if (strpos($pCoordinate, '!') !== false) {
            $worksheetReference = self::extractSheetTitle($pCoordinate, true);

            return $this->parent->getSheetByName($worksheetReference[0])->cellExists(strtoupper($worksheetReference[1]));
        }

        // Named range?
        if ((!preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $pCoordinate, $matches)) &&
            (preg_match('/^' . Calculation::CALCULATION_REGEXP_NAMEDRANGE . '$/i', $pCoordinate, $matches))) {
            $namedRange = NamedRange::resolveRange($pCoordinate, $this);
            if ($namedRange !== null) {
                $pCoordinate = $namedRange->getRange();
                if ($this->getHashCode() != $namedRange->getWorksheet()->getHashCode()) {
                    if (!$namedRange->getLocalOnly()) {
                        return $namedRange->getWorksheet()->cellExists($pCoordinate);
                    }
                    throw new Exception('Named range ' . $namedRange->getName() . ' is not accessible from within sheet ' . $this->getTitle());
                }
            } else {
                return false;
            }
        }

        // Uppercase coordinate
        $pCoordinate = strtoupper($pCoordinate);

        if (strpos($pCoordinate, ':') !== false || strpos($pCoordinate, ',') !== false) {
            throw new Exception('Cell coordinate can not be a range of cells.');
        } elseif (strpos($pCoordinate, '$') !== false) {
            throw new Exception('Cell coordinate must not be absolute.');
        }
            // Coordinates
            $aCoordinates = Cell::coordinateFromString($pCoordinate);

            // Cell exists?
            return $this->cellCollection->isDataSet($pCoordinate);
    }

    /**
     * Cell at a specific coordinate by using numeric cell coordinates exists?
     *
     * @param string $pColumn Numeric column coordinate of the cell
     * @param string $pRow Numeric row coordinate of the cell
     *
     * @return bool
     */
    public function cellExistsByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->cellExists(Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Get row dimension at a specific row.
     *
     * @param int $pRow Numeric index of the row
     * @param mixed $create
     *
     * @return Worksheet\RowDimension
     */
    public function getRowDimension($pRow = 1, $create = true)
    {
        // Found
        $found = null;

        // Get row dimension
        if (!isset($this->rowDimensions[$pRow])) {
            if (!$create) {
                return null;
            }
            $this->rowDimensions[$pRow] = new Worksheet\RowDimension($pRow);

            $this->cachedHighestRow = max($this->cachedHighestRow, $pRow);
        }

        return $this->rowDimensions[$pRow];
    }

    /**
     * Get column dimension at a specific column.
     *
     * @param string $pColumn String index of the column
     * @param mixed $create
     *
     * @return Worksheet\ColumnDimension
     */
    public function getColumnDimension($pColumn = 'A', $create = true)
    {
        // Uppercase coordinate
        $pColumn = strtoupper($pColumn);

        // Fetch dimensions
        if (!isset($this->columnDimensions[$pColumn])) {
            if (!$create) {
                return null;
            }
            $this->columnDimensions[$pColumn] = new Worksheet\ColumnDimension($pColumn);

            if (Cell::columnIndexFromString($this->cachedHighestColumn) < Cell::columnIndexFromString($pColumn)) {
                $this->cachedHighestColumn = $pColumn;
            }
        }

        return $this->columnDimensions[$pColumn];
    }

    /**
     * Get column dimension at a specific column by using numeric cell coordinates.
     *
     * @param int $pColumn Numeric column coordinate of the cell
     *
     * @return Worksheet\ColumnDimension
     */
    public function getColumnDimensionByColumn($pColumn = 0)
    {
        return $this->getColumnDimension(Cell::stringFromColumnIndex($pColumn));
    }

    /**
     * Get styles.
     *
     * @return Style[]
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * Get style for cell.
     *
     * @param string $pCellCoordinate Cell coordinate (or range) to get style for
     *
     * @throws Exception
     *
     * @return Style
     */
    public function getStyle($pCellCoordinate = 'A1')
    {
        // set this sheet as active
        $this->parent->setActiveSheetIndex($this->parent->getIndex($this));

        // set cell coordinate as active
        $this->setSelectedCells(strtoupper($pCellCoordinate));

        return $this->parent->getCellXfSupervisor();
    }

    /**
     * Get conditional styles for a cell.
     *
     * @param string $pCoordinate
     *
     * @return Style\Conditional[]
     */
    public function getConditionalStyles($pCoordinate = 'A1')
    {
        $pCoordinate = strtoupper($pCoordinate);
        if (!isset($this->conditionalStylesCollection[$pCoordinate])) {
            $this->conditionalStylesCollection[$pCoordinate] = [];
        }

        return $this->conditionalStylesCollection[$pCoordinate];
    }

    /**
     * Do conditional styles exist for this cell?
     *
     * @param string $pCoordinate
     *
     * @return bool
     */
    public function conditionalStylesExists($pCoordinate = 'A1')
    {
        if (isset($this->conditionalStylesCollection[strtoupper($pCoordinate)])) {
            return true;
        }

        return false;
    }

    /**
     * Removes conditional styles for a cell.
     *
     * @param string $pCoordinate
     *
     * @return Worksheet
     */
    public function removeConditionalStyles($pCoordinate = 'A1')
    {
        unset($this->conditionalStylesCollection[strtoupper($pCoordinate)]);

        return $this;
    }

    /**
     * Get collection of conditional styles.
     *
     * @return array
     */
    public function getConditionalStylesCollection()
    {
        return $this->conditionalStylesCollection;
    }

    /**
     * Set conditional styles.
     *
     * @param string $pCoordinate eg: 'A1'
     * @param $pValue Style\Conditional[]
     *
     * @return Worksheet
     */
    public function setConditionalStyles($pCoordinate, $pValue)
    {
        $this->conditionalStylesCollection[strtoupper($pCoordinate)] = $pValue;

        return $this;
    }

    /**
     * Get style for cell by using numeric cell coordinates.
     *
     * @param int $pColumn Numeric column coordinate of the cell
     * @param int $pRow Numeric row coordinate of the cell
     * @param int pColumn2 Numeric column coordinate of the range cell
     * @param int pRow2 Numeric row coordinate of the range cell
     * @param null|mixed $pColumn2
     * @param null|mixed $pRow2
     *
     * @return Style
     */
    public function getStyleByColumnAndRow($pColumn = 0, $pRow = 1, $pColumn2 = null, $pRow2 = null)
    {
        if (!is_null($pColumn2) && !is_null($pRow2)) {
            $cellRange = Cell::stringFromColumnIndex($pColumn) . $pRow . ':' . Cell::stringFromColumnIndex($pColumn2) . $pRow2;

            return $this->getStyle($cellRange);
        }

        return $this->getStyle(Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Duplicate cell style to a range of cells.
     *
     * Please note that this will overwrite existing cell styles for cells in range!
     *
     * @param Style $pCellStyle Cell style to duplicate
     * @param string $pRange Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function duplicateStyle(Style $pCellStyle = null, $pRange = '')
    {
        // make sure we have a real style and not supervisor
        $style = $pCellStyle->getIsSupervisor() ? $pCellStyle->getSharedComponent() : $pCellStyle;

        // Add the style to the workbook if necessary
        $workbook = $this->parent;
        if ($existingStyle = $this->parent->getCellXfByHashCode($pCellStyle->getHashCode())) {
            // there is already such cell Xf in our collection
            $xfIndex = $existingStyle->getIndex();
        } else {
            // we don't have such a cell Xf, need to add
            $workbook->addCellXf($pCellStyle);
            $xfIndex = $pCellStyle->getIndex();
        }

        // Calculate range outer borders
        list($rangeStart, $rangeEnd) = Cell::rangeBoundaries($pRange . ':' . $pRange);

        // Make sure we can loop upwards on rows and columns
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd = $tmp;
        }

        // Loop through cells and apply styles
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->getCell(Cell::stringFromColumnIndex($col - 1) . $row)->setXfIndex($xfIndex);
            }
        }

        return $this;
    }

    /**
     * Duplicate conditional style to a range of cells.
     *
     * Please note that this will overwrite existing cell styles for cells in range!
     *
     * @param Style\Conditional[] $pCellStyle Cell style to duplicate
     * @param string $pRange Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function duplicateConditionalStyle(array $pCellStyle = null, $pRange = '')
    {
        foreach ($pCellStyle as $cellStyle) {
            if (!($cellStyle instanceof Style\Conditional)) {
                throw new Exception('Style is not a conditional style');
            }
        }

        // Calculate range outer borders
        list($rangeStart, $rangeEnd) = Cell::rangeBoundaries($pRange . ':' . $pRange);

        // Make sure we can loop upwards on rows and columns
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd = $tmp;
        }

        // Loop through cells and apply styles
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->setConditionalStyles(Cell::stringFromColumnIndex($col - 1) . $row, $pCellStyle);
            }
        }

        return $this;
    }

    /**
     * Set break on a cell.
     *
     * @param string $pCell Cell coordinate (e.g. A1)
     * @param int $pBreak Break type (type of Worksheet::BREAK_*)
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setBreak($pCell = 'A1', $pBreak = self::BREAK_NONE)
    {
        // Uppercase coordinate
        $pCell = strtoupper($pCell);

        if ($pCell != '') {
            if ($pBreak == self::BREAK_NONE) {
                if (isset($this->breaks[$pCell])) {
                    unset($this->breaks[$pCell]);
                }
            } else {
                $this->breaks[$pCell] = $pBreak;
            }
        } else {
            throw new Exception('No cell coordinate specified.');
        }

        return $this;
    }

    /**
     * Set break on a cell by using numeric cell coordinates.
     *
     * @param int $pColumn Numeric column coordinate of the cell
     * @param int $pRow Numeric row coordinate of the cell
     * @param int $pBreak Break type (type of \PhpOffice\PhpSpreadsheet\Worksheet::BREAK_*)
     *
     * @return Worksheet
     */
    public function setBreakByColumnAndRow($pColumn = 0, $pRow = 1, $pBreak = self::BREAK_NONE)
    {
        return $this->setBreak(Cell::stringFromColumnIndex($pColumn) . $pRow, $pBreak);
    }

    /**
     * Get breaks.
     *
     * @return array[]
     */
    public function getBreaks()
    {
        return $this->breaks;
    }

    /**
     * Set merge on a cell range.
     *
     * @param string $pRange Cell range (e.g. A1:E1)
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function mergeCells($pRange = 'A1:A1')
    {
        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        if (strpos($pRange, ':') !== false) {
            $this->mergeCells[$pRange] = $pRange;

            // make sure cells are created

            // get the cells in the range
            $aReferences = Cell::extractAllCellReferencesInRange($pRange);

            // create upper left cell if it does not already exist
            $upperLeft = $aReferences[0];
            if (!$this->cellExists($upperLeft)) {
                $this->getCell($upperLeft)->setValueExplicit(null, Cell\DataType::TYPE_NULL);
            }

            // Blank out the rest of the cells in the range (if they exist)
            $count = count($aReferences);
            for ($i = 1; $i < $count; ++$i) {
                if ($this->cellExists($aReferences[$i])) {
                    $this->getCell($aReferences[$i])->setValueExplicit(null, Cell\DataType::TYPE_NULL);
                }
            }
        } else {
            throw new Exception('Merge must be set on a range of cells.');
        }

        return $this;
    }

    /**
     * Set merge on a cell range by using numeric cell coordinates.
     *
     * @param int $pColumn1 Numeric column coordinate of the first cell
     * @param int $pRow1 Numeric row coordinate of the first cell
     * @param int $pColumn2 Numeric column coordinate of the last cell
     * @param int $pRow2 Numeric row coordinate of the last cell
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function mergeCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1)
    {
        $cellRange = Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . Cell::stringFromColumnIndex($pColumn2) . $pRow2;

        return $this->mergeCells($cellRange);
    }

    /**
     * Remove merge on a cell range.
     *
     * @param string $pRange Cell range (e.g. A1:E1)
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function unmergeCells($pRange = 'A1:A1')
    {
        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        if (strpos($pRange, ':') !== false) {
            if (isset($this->mergeCells[$pRange])) {
                unset($this->mergeCells[$pRange]);
            } else {
                throw new Exception('Cell range ' . $pRange . ' not known as merged.');
            }
        } else {
            throw new Exception('Merge can only be removed from a range of cells.');
        }

        return $this;
    }

    /**
     * Remove merge on a cell range by using numeric cell coordinates.
     *
     * @param int $pColumn1 Numeric column coordinate of the first cell
     * @param int $pRow1 Numeric row coordinate of the first cell
     * @param int $pColumn2 Numeric column coordinate of the last cell
     * @param int $pRow2 Numeric row coordinate of the last cell
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function unmergeCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1)
    {
        $cellRange = Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . Cell::stringFromColumnIndex($pColumn2) . $pRow2;

        return $this->unmergeCells($cellRange);
    }

    /**
     * Get merge cells array.
     *
     * @return array[]
     */
    public function getMergeCells()
    {
        return $this->mergeCells;
    }

    /**
     * Set merge cells array for the entire sheet. Use instead mergeCells() to merge
     * a single cell range.
     *
     * @param array
     * @param mixed $pValue
     */
    public function setMergeCells($pValue = [])
    {
        $this->mergeCells = $pValue;

        return $this;
    }

    /**
     * Set protection on a cell range.
     *
     * @param string $pRange Cell (e.g. A1) or cell range (e.g. A1:E1)
     * @param string $pPassword Password to unlock the protection
     * @param bool $pAlreadyHashed If the password has already been hashed, set this to true
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function protectCells($pRange = 'A1', $pPassword = '', $pAlreadyHashed = false)
    {
        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        if (!$pAlreadyHashed) {
            $pPassword = Shared\PasswordHasher::hashPassword($pPassword);
        }
        $this->protectedCells[$pRange] = $pPassword;

        return $this;
    }

    /**
     * Set protection on a cell range by using numeric cell coordinates.
     *
     * @param int $pColumn1 Numeric column coordinate of the first cell
     * @param int $pRow1 Numeric row coordinate of the first cell
     * @param int $pColumn2 Numeric column coordinate of the last cell
     * @param int $pRow2 Numeric row coordinate of the last cell
     * @param string $pPassword Password to unlock the protection
     * @param bool $pAlreadyHashed If the password has already been hashed, set this to true
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function protectCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1, $pPassword = '', $pAlreadyHashed = false)
    {
        $cellRange = Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . Cell::stringFromColumnIndex($pColumn2) . $pRow2;

        return $this->protectCells($cellRange, $pPassword, $pAlreadyHashed);
    }

    /**
     * Remove protection on a cell range.
     *
     * @param string $pRange Cell (e.g. A1) or cell range (e.g. A1:E1)
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function unprotectCells($pRange = 'A1')
    {
        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        if (isset($this->protectedCells[$pRange])) {
            unset($this->protectedCells[$pRange]);
        } else {
            throw new Exception('Cell range ' . $pRange . ' not known as protected.');
        }

        return $this;
    }

    /**
     * Remove protection on a cell range by using numeric cell coordinates.
     *
     * @param int $pColumn1 Numeric column coordinate of the first cell
     * @param int $pRow1 Numeric row coordinate of the first cell
     * @param int $pColumn2 Numeric column coordinate of the last cell
     * @param int $pRow2 Numeric row coordinate of the last cell
     * @param string $pPassword Password to unlock the protection
     * @param bool $pAlreadyHashed If the password has already been hashed, set this to true
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function unprotectCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1, $pPassword = '', $pAlreadyHashed = false)
    {
        $cellRange = Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . Cell::stringFromColumnIndex($pColumn2) . $pRow2;

        return $this->unprotectCells($cellRange);
    }

    /**
     * Get protected cells.
     *
     * @return array[]
     */
    public function getProtectedCells()
    {
        return $this->protectedCells;
    }

    /**
     * Get Autofilter.
     *
     * @return Worksheet\AutoFilter
     */
    public function getAutoFilter()
    {
        return $this->autoFilter;
    }

    /**
     * Set AutoFilter.
     *
     * @param Worksheet\AutoFilter|string $pValue
     *            A simple string containing a Cell range like 'A1:E10' is permitted for backward compatibility
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setAutoFilter($pValue)
    {
        $pRange = strtoupper($pValue);
        if (is_string($pValue)) {
            $this->autoFilter->setRange($pValue);
        } elseif (is_object($pValue) && ($pValue instanceof Worksheet\AutoFilter)) {
            $this->autoFilter = $pValue;
        }

        return $this;
    }

    /**
     * Set Autofilter Range by using numeric cell coordinates.
     *
     * @param int $pColumn1 Numeric column coordinate of the first cell
     * @param int $pRow1 Numeric row coordinate of the first cell
     * @param int $pColumn2 Numeric column coordinate of the second cell
     * @param int $pRow2 Numeric row coordinate of the second cell
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setAutoFilterByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1)
    {
        return $this->setAutoFilter(
            Cell::stringFromColumnIndex($pColumn1) . $pRow1
            . ':' .
            Cell::stringFromColumnIndex($pColumn2) . $pRow2
        );
    }

    /**
     * Remove autofilter.
     *
     * @return Worksheet
     */
    public function removeAutoFilter()
    {
        $this->autoFilter->setRange(null);

        return $this;
    }

    /**
     * Get Freeze Pane.
     *
     * @return string
     */
    public function getFreezePane()
    {
        return $this->freezePane;
    }

    /**
     * Freeze Pane.
     *
     * @param string $pCell Cell (i.e. A2)
     *                                    Examples:
     *                                        A2 will freeze the rows above cell A2 (i.e row 1)
     *                                        B1 will freeze the columns to the left of cell B1 (i.e column A)
     *                                        B2 will freeze the rows above and to the left of cell A2
     *                                            (i.e row 1 and column A)
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function freezePane($pCell = '')
    {
        // Uppercase coordinate
        $pCell = strtoupper($pCell);
        if (strpos($pCell, ':') === false && strpos($pCell, ',') === false) {
            $this->freezePane = $pCell;
        } else {
            throw new Exception('Freeze pane can not be set on a range of cells.');
        }

        return $this;
    }

    /**
     * Freeze Pane by using numeric cell coordinates.
     *
     * @param int $pColumn Numeric column coordinate of the cell
     * @param int $pRow Numeric row coordinate of the cell
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function freezePaneByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->freezePane(Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Unfreeze Pane.
     *
     * @return Worksheet
     */
    public function unfreezePane()
    {
        return $this->freezePane('');
    }

    /**
     * Insert a new row, updating all possible related data.
     *
     * @param int $pBefore Insert before this one
     * @param int $pNumRows Number of rows to insert
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function insertNewRowBefore($pBefore = 1, $pNumRows = 1)
    {
        if ($pBefore >= 1) {
            $objReferenceHelper = ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore('A' . $pBefore, 0, $pNumRows, $this);
        } else {
            throw new Exception('Rows can only be inserted before at least row 1.');
        }

        return $this;
    }

    /**
     * Insert a new column, updating all possible related data.
     *
     * @param int $pBefore Insert before this one
     * @param int $pNumCols Number of columns to insert
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function insertNewColumnBefore($pBefore = 'A', $pNumCols = 1)
    {
        if (!is_numeric($pBefore)) {
            $objReferenceHelper = ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore($pBefore . '1', $pNumCols, 0, $this);
        } else {
            throw new Exception('Column references should not be numeric.');
        }

        return $this;
    }

    /**
     * Insert a new column, updating all possible related data.
     *
     * @param int $pBefore Insert before this one (numeric column coordinate of the cell)
     * @param int $pNumCols Number of columns to insert
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function insertNewColumnBeforeByIndex($pBefore = 0, $pNumCols = 1)
    {
        if ($pBefore >= 0) {
            return $this->insertNewColumnBefore(Cell::stringFromColumnIndex($pBefore), $pNumCols);
        }
        throw new Exception('Columns can only be inserted before at least column A (0).');
    }

    /**
     * Delete a row, updating all possible related data.
     *
     * @param int $pRow Remove starting with this one
     * @param int $pNumRows Number of rows to remove
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function removeRow($pRow = 1, $pNumRows = 1)
    {
        if ($pRow >= 1) {
            $highestRow = $this->getHighestDataRow();
            $objReferenceHelper = ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore('A' . ($pRow + $pNumRows), 0, -$pNumRows, $this);
            for ($r = 0; $r < $pNumRows; ++$r) {
                $this->getCellCacheController()->removeRow($highestRow);
                --$highestRow;
            }
        } else {
            throw new Exception('Rows to be deleted should at least start from row 1.');
        }

        return $this;
    }

    /**
     * Remove a column, updating all possible related data.
     *
     * @param string $pColumn Remove starting with this one
     * @param int $pNumCols Number of columns to remove
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function removeColumn($pColumn = 'A', $pNumCols = 1)
    {
        if (!is_numeric($pColumn)) {
            $highestColumn = $this->getHighestDataColumn();
            $pColumn = Cell::stringFromColumnIndex(Cell::columnIndexFromString($pColumn) - 1 + $pNumCols);
            $objReferenceHelper = ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore($pColumn . '1', -$pNumCols, 0, $this);
            for ($c = 0; $c < $pNumCols; ++$c) {
                $this->getCellCacheController()->removeColumn($highestColumn);
                $highestColumn = Cell::stringFromColumnIndex(Cell::columnIndexFromString($highestColumn) - 2);
            }
        } else {
            throw new Exception('Column references should not be numeric.');
        }

        return $this;
    }

    /**
     * Remove a column, updating all possible related data.
     *
     * @param int $pColumn Remove starting with this one (numeric column coordinate of the cell)
     * @param int $pNumCols Number of columns to remove
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function removeColumnByIndex($pColumn = 0, $pNumCols = 1)
    {
        if ($pColumn >= 0) {
            return $this->removeColumn(Cell::stringFromColumnIndex($pColumn), $pNumCols);
        }
        throw new Exception('Columns to be deleted should at least start from column 0');
    }

    /**
     * Show gridlines?
     *
     * @return bool
     */
    public function getShowGridlines()
    {
        return $this->showGridlines;
    }

    /**
     * Set show gridlines.
     *
     * @param bool $pValue Show gridlines (true/false)
     *
     * @return Worksheet
     */
    public function setShowGridlines($pValue = false)
    {
        $this->showGridlines = $pValue;

        return $this;
    }

    /**
     * Print gridlines?
     *
     * @return bool
     */
    public function getPrintGridlines()
    {
        return $this->printGridlines;
    }

    /**
     * Set print gridlines.
     *
     * @param bool $pValue Print gridlines (true/false)
     *
     * @return Worksheet
     */
    public function setPrintGridlines($pValue = false)
    {
        $this->printGridlines = $pValue;

        return $this;
    }

    /**
     * Show row and column headers?
     *
     * @return bool
     */
    public function getShowRowColHeaders()
    {
        return $this->showRowColHeaders;
    }

    /**
     * Set show row and column headers.
     *
     * @param bool $pValue Show row and column headers (true/false)
     *
     * @return Worksheet
     */
    public function setShowRowColHeaders($pValue = false)
    {
        $this->showRowColHeaders = $pValue;

        return $this;
    }

    /**
     * Show summary below? (Row/Column outlining).
     *
     * @return bool
     */
    public function getShowSummaryBelow()
    {
        return $this->showSummaryBelow;
    }

    /**
     * Set show summary below.
     *
     * @param bool $pValue Show summary below (true/false)
     *
     * @return Worksheet
     */
    public function setShowSummaryBelow($pValue = true)
    {
        $this->showSummaryBelow = $pValue;

        return $this;
    }

    /**
     * Show summary right? (Row/Column outlining).
     *
     * @return bool
     */
    public function getShowSummaryRight()
    {
        return $this->showSummaryRight;
    }

    /**
     * Set show summary right.
     *
     * @param bool $pValue Show summary right (true/false)
     *
     * @return Worksheet
     */
    public function setShowSummaryRight($pValue = true)
    {
        $this->showSummaryRight = $pValue;

        return $this;
    }

    /**
     * Get comments.
     *
     * @return Comment[]
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set comments array for the entire sheet.
     *
     * @param array of Comment
     * @param mixed $pValue
     *
     * @return Worksheet
     */
    public function setComments($pValue = [])
    {
        $this->comments = $pValue;

        return $this;
    }

    /**
     * Get comment for cell.
     *
     * @param string $pCellCoordinate Cell coordinate to get comment for
     *
     * @throws Exception
     *
     * @return Comment
     */
    public function getComment($pCellCoordinate = 'A1')
    {
        // Uppercase coordinate
        $pCellCoordinate = strtoupper($pCellCoordinate);

        if (strpos($pCellCoordinate, ':') !== false || strpos($pCellCoordinate, ',') !== false) {
            throw new Exception('Cell coordinate string can not be a range of cells.');
        } elseif (strpos($pCellCoordinate, '$') !== false) {
            throw new Exception('Cell coordinate string must not be absolute.');
        } elseif ($pCellCoordinate == '') {
            throw new Exception('Cell coordinate can not be zero-length string.');
        }

        // Check if we already have a comment for this cell.
        if (isset($this->comments[$pCellCoordinate])) {
            return $this->comments[$pCellCoordinate];
        }

        // If not, create a new comment.
        $newComment = new Comment();
        $this->comments[$pCellCoordinate] = $newComment;

        return $newComment;
    }

    /**
     * Get comment for cell by using numeric cell coordinates.
     *
     * @param int $pColumn Numeric column coordinate of the cell
     * @param int $pRow Numeric row coordinate of the cell
     *
     * @return Comment
     */
    public function getCommentByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->getComment(Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Get active cell.
     *
     * @return string Example: 'A1'
     */
    public function getActiveCell()
    {
        return $this->activeCell;
    }

    /**
     * Get selected cells.
     *
     * @return string
     */
    public function getSelectedCells()
    {
        return $this->selectedCells;
    }

    /**
     * Selected cell.
     *
     * @param string $pCoordinate Cell (i.e. A1)
     *
     * @return Worksheet
     */
    public function setSelectedCell($pCoordinate = 'A1')
    {
        return $this->setSelectedCells($pCoordinate);
    }

    /**
     * Select a range of cells.
     *
     * @param string $pCoordinate Cell range, examples: 'A1', 'B2:G5', 'A:C', '3:6'
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setSelectedCells($pCoordinate = 'A1')
    {
        // Uppercase coordinate
        $pCoordinate = strtoupper($pCoordinate);

        // Convert 'A' to 'A:A'
        $pCoordinate = preg_replace('/^([A-Z]+)$/', '${1}:${1}', $pCoordinate);

        // Convert '1' to '1:1'
        $pCoordinate = preg_replace('/^([0-9]+)$/', '${1}:${1}', $pCoordinate);

        // Convert 'A:C' to 'A1:C1048576'
        $pCoordinate = preg_replace('/^([A-Z]+):([A-Z]+)$/', '${1}1:${2}1048576', $pCoordinate);

        // Convert '1:3' to 'A1:XFD3'
        $pCoordinate = preg_replace('/^([0-9]+):([0-9]+)$/', 'A${1}:XFD${2}', $pCoordinate);

        if (strpos($pCoordinate, ':') !== false || strpos($pCoordinate, ',') !== false) {
            list($first) = Cell::splitRange($pCoordinate);
            $this->activeCell = $first[0];
        } else {
            $this->activeCell = $pCoordinate;
        }
        $this->selectedCells = $pCoordinate;

        return $this;
    }

    /**
     * Selected cell by using numeric cell coordinates.
     *
     * @param int $pColumn Numeric column coordinate of the cell
     * @param int $pRow Numeric row coordinate of the cell
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setSelectedCellByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->setSelectedCells(Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Get right-to-left.
     *
     * @return bool
     */
    public function getRightToLeft()
    {
        return $this->rightToLeft;
    }

    /**
     * Set right-to-left.
     *
     * @param bool $value Right-to-left true/false
     *
     * @return Worksheet
     */
    public function setRightToLeft($value = false)
    {
        $this->rightToLeft = $value;

        return $this;
    }

    /**
     * Fill worksheet from values in array.
     *
     * @param array $source Source array
     * @param mixed $nullValue Value in source array that stands for blank cell
     * @param string $startCell Insert array starting from this cell address as the top left coordinate
     * @param bool $strictNullComparison Apply strict comparison when testing for null values in the array
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function fromArray($source = null, $nullValue = null, $startCell = 'A1', $strictNullComparison = false)
    {
        if (is_array($source)) {
            //    Convert a 1-D array to 2-D (for ease of looping)
            if (!is_array(end($source))) {
                $source = [$source];
            }

            // start coordinate
            list($startColumn, $startRow) = Cell::coordinateFromString($startCell);

            // Loop through $source
            foreach ($source as $rowData) {
                $currentColumn = $startColumn;
                foreach ($rowData as $cellValue) {
                    if ($strictNullComparison) {
                        if ($cellValue !== $nullValue) {
                            // Set cell value
                            $this->getCell($currentColumn . $startRow)->setValue($cellValue);
                        }
                    } else {
                        if ($cellValue != $nullValue) {
                            // Set cell value
                            $this->getCell($currentColumn . $startRow)->setValue($cellValue);
                        }
                    }
                    ++$currentColumn;
                }
                ++$startRow;
            }
        } else {
            throw new Exception('Parameter $source should be an array.');
        }

        return $this;
    }

    /**
     * Create array from a range of cells.
     *
     * @param string $pRange Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     * @param mixed $nullValue Value returned in the array entry if a cell doesn't exist
     * @param bool $calculateFormulas Should formulas be calculated?
     * @param bool $formatData Should formatting be applied to cell values?
     * @param bool $returnCellRef False - Return a simple array of rows and columns indexed by number counting from zero
     *                               True - Return rows and columns indexed by their actual row and column IDs
     *
     * @return array
     */
    public function rangeToArray($pRange = 'A1', $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        // Returnvalue
        $returnValue = [];
        //    Identify the range that we need to extract from the worksheet
        list($rangeStart, $rangeEnd) = Cell::rangeBoundaries($pRange);
        $minCol = Cell::stringFromColumnIndex($rangeStart[0] - 1);
        $minRow = $rangeStart[1];
        $maxCol = Cell::stringFromColumnIndex($rangeEnd[0] - 1);
        $maxRow = $rangeEnd[1];

        ++$maxCol;
        // Loop through rows
        $r = -1;
        for ($row = $minRow; $row <= $maxRow; ++$row) {
            $rRef = ($returnCellRef) ? $row : ++$r;
            $c = -1;
            // Loop through columns in the current row
            for ($col = $minCol; $col != $maxCol; ++$col) {
                $cRef = ($returnCellRef) ? $col : ++$c;
                //    Using getCell() will create a new cell if it doesn't already exist. We don't want that to happen
                //        so we test and retrieve directly against cellCollection
                if ($this->cellCollection->isDataSet($col . $row)) {
                    // Cell exists
                    $cell = $this->cellCollection->getCacheData($col . $row);
                    if ($cell->getValue() !== null) {
                        if ($cell->getValue() instanceof RichText) {
                            $returnValue[$rRef][$cRef] = $cell->getValue()->getPlainText();
                        } else {
                            if ($calculateFormulas) {
                                $returnValue[$rRef][$cRef] = $cell->getCalculatedValue();
                            } else {
                                $returnValue[$rRef][$cRef] = $cell->getValue();
                            }
                        }

                        if ($formatData) {
                            $style = $this->parent->getCellXfByIndex($cell->getXfIndex());
                            $returnValue[$rRef][$cRef] = Style\NumberFormat::toFormattedString(
                                $returnValue[$rRef][$cRef],
                                ($style && $style->getNumberFormat()) ? $style->getNumberFormat()->getFormatCode() : Style\NumberFormat::FORMAT_GENERAL
                            );
                        }
                    } else {
                        // Cell holds a NULL
                        $returnValue[$rRef][$cRef] = $nullValue;
                    }
                } else {
                    // Cell doesn't exist
                    $returnValue[$rRef][$cRef] = $nullValue;
                }
            }
        }

        // Return
        return $returnValue;
    }

    /**
     * Create array from a range of cells.
     *
     * @param string $pNamedRange Name of the Named Range
     * @param mixed $nullValue Value returned in the array entry if a cell doesn't exist
     * @param bool $calculateFormulas Should formulas be calculated?
     * @param bool $formatData Should formatting be applied to cell values?
     * @param bool $returnCellRef False - Return a simple array of rows and columns indexed by number counting from zero
     *                                True - Return rows and columns indexed by their actual row and column IDs
     *
     * @throws Exception
     *
     * @return array
     */
    public function namedRangeToArray($pNamedRange = '', $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        $namedRange = NamedRange::resolveRange($pNamedRange, $this);
        if ($namedRange !== null) {
            $pWorkSheet = $namedRange->getWorksheet();
            $pCellRange = $namedRange->getRange();

            return $pWorkSheet->rangeToArray($pCellRange, $nullValue, $calculateFormulas, $formatData, $returnCellRef);
        }

        throw new Exception('Named Range ' . $pNamedRange . ' does not exist.');
    }

    /**
     * Create array from worksheet.
     *
     * @param mixed $nullValue Value returned in the array entry if a cell doesn't exist
     * @param bool $calculateFormulas Should formulas be calculated?
     * @param bool $formatData Should formatting be applied to cell values?
     * @param bool $returnCellRef False - Return a simple array of rows and columns indexed by number counting from zero
     *                               True - Return rows and columns indexed by their actual row and column IDs
     *
     * @return array
     */
    public function toArray($nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        // Garbage collect...
        $this->garbageCollect();

        //    Identify the range that we need to extract from the worksheet
        $maxCol = $this->getHighestColumn();
        $maxRow = $this->getHighestRow();
        // Return
        return $this->rangeToArray('A1:' . $maxCol . $maxRow, $nullValue, $calculateFormulas, $formatData, $returnCellRef);
    }

    /**
     * Get row iterator.
     *
     * @param int $startRow The row number at which to start iterating
     * @param int $endRow The row number at which to stop iterating
     *
     * @return Worksheet\RowIterator
     */
    public function getRowIterator($startRow = 1, $endRow = null)
    {
        return new Worksheet\RowIterator($this, $startRow, $endRow);
    }

    /**
     * Get column iterator.
     *
     * @param string $startColumn The column address at which to start iterating
     * @param string $endColumn The column address at which to stop iterating
     *
     * @return Worksheet\ColumnIterator
     */
    public function getColumnIterator($startColumn = 'A', $endColumn = null)
    {
        return new Worksheet\ColumnIterator($this, $startColumn, $endColumn);
    }

    /**
     * Run PhpSpreadsheet garabage collector.
     *
     * @return Worksheet
     */
    public function garbageCollect()
    {
        // Flush cache
        $this->cellCollection->getCacheData('A1');

        // Lookup highest column and highest row if cells are cleaned
        $colRow = $this->cellCollection->getHighestRowAndColumn();
        $highestRow = $colRow['row'];
        $highestColumn = Cell::columnIndexFromString($colRow['column']);

        // Loop through column dimensions
        foreach ($this->columnDimensions as $dimension) {
            $highestColumn = max($highestColumn, Cell::columnIndexFromString($dimension->getColumnIndex()));
        }

        // Loop through row dimensions
        foreach ($this->rowDimensions as $dimension) {
            $highestRow = max($highestRow, $dimension->getRowIndex());
        }

        // Cache values
        if ($highestColumn < 0) {
            $this->cachedHighestColumn = 'A';
        } else {
            $this->cachedHighestColumn = Cell::stringFromColumnIndex(--$highestColumn);
        }
        $this->cachedHighestRow = $highestRow;

        // Return
        return $this;
    }

    /**
     * Get hash code.
     *
     * @return string Hash code
     */
    public function getHashCode()
    {
        if ($this->dirty) {
            $this->hash = md5($this->title . $this->autoFilter . ($this->protection->isProtectionEnabled() ? 't' : 'f') . __CLASS__);
            $this->dirty = false;
        }

        return $this->hash;
    }

    /**
     * Extract worksheet title from range.
     *
     * Example: extractSheetTitle("testSheet!A1") ==> 'A1'
     * Example: extractSheetTitle("'testSheet 1'!A1", true) ==> array('testSheet 1', 'A1');
     *
     * @param string $pRange Range to extract title from
     * @param bool $returnRange Return range? (see example)
     *
     * @return mixed
     */
    public static function extractSheetTitle($pRange, $returnRange = false)
    {
        // Sheet title included?
        if (($sep = strpos($pRange, '!')) === false) {
            return '';
        }

        if ($returnRange) {
            return [trim(substr($pRange, 0, $sep), "'"), substr($pRange, $sep + 1)];
        }

        return substr($pRange, $sep + 1);
    }

    /**
     * Get hyperlink.
     *
     * @param string $pCellCoordinate Cell coordinate to get hyperlink for
     */
    public function getHyperlink($pCellCoordinate = 'A1')
    {
        // return hyperlink if we already have one
        if (isset($this->hyperlinkCollection[$pCellCoordinate])) {
            return $this->hyperlinkCollection[$pCellCoordinate];
        }

        // else create hyperlink
        $this->hyperlinkCollection[$pCellCoordinate] = new Cell\Hyperlink();

        return $this->hyperlinkCollection[$pCellCoordinate];
    }

    /**
     * Set hyperlnk.
     *
     * @param string $pCellCoordinate Cell coordinate to insert hyperlink
     * @param Cell\Hyperlink $pHyperlink
     *
     * @return Worksheet
     */
    public function setHyperlink($pCellCoordinate = 'A1', Cell\Hyperlink $pHyperlink = null)
    {
        if ($pHyperlink === null) {
            unset($this->hyperlinkCollection[$pCellCoordinate]);
        } else {
            $this->hyperlinkCollection[$pCellCoordinate] = $pHyperlink;
        }

        return $this;
    }

    /**
     * Hyperlink at a specific coordinate exists?
     *
     * @param string $pCoordinate
     *
     * @return bool
     */
    public function hyperlinkExists($pCoordinate = 'A1')
    {
        return isset($this->hyperlinkCollection[$pCoordinate]);
    }

    /**
     * Get collection of hyperlinks.
     *
     * @return Cell\Hyperlink[]
     */
    public function getHyperlinkCollection()
    {
        return $this->hyperlinkCollection;
    }

    /**
     * Get data validation.
     *
     * @param string $pCellCoordinate Cell coordinate to get data validation for
     */
    public function getDataValidation($pCellCoordinate = 'A1')
    {
        // return data validation if we already have one
        if (isset($this->dataValidationCollection[$pCellCoordinate])) {
            return $this->dataValidationCollection[$pCellCoordinate];
        }

        // else create data validation
        $this->dataValidationCollection[$pCellCoordinate] = new Cell\DataValidation();

        return $this->dataValidationCollection[$pCellCoordinate];
    }

    /**
     * Set data validation.
     *
     * @param string $pCellCoordinate Cell coordinate to insert data validation
     * @param Cell\DataValidation $pDataValidation
     *
     * @return Worksheet
     */
    public function setDataValidation($pCellCoordinate = 'A1', Cell\DataValidation $pDataValidation = null)
    {
        if ($pDataValidation === null) {
            unset($this->dataValidationCollection[$pCellCoordinate]);
        } else {
            $this->dataValidationCollection[$pCellCoordinate] = $pDataValidation;
        }

        return $this;
    }

    /**
     * Data validation at a specific coordinate exists?
     *
     * @param string $pCoordinate
     *
     * @return bool
     */
    public function dataValidationExists($pCoordinate = 'A1')
    {
        return isset($this->dataValidationCollection[$pCoordinate]);
    }

    /**
     * Get collection of data validations.
     *
     * @return Cell\DataValidation[]
     */
    public function getDataValidationCollection()
    {
        return $this->dataValidationCollection;
    }

    /**
     * Accepts a range, returning it as a range that falls within the current highest row and column of the worksheet.
     *
     * @param string $range
     *
     * @return string Adjusted range value
     */
    public function shrinkRangeToFit($range)
    {
        $maxCol = $this->getHighestColumn();
        $maxRow = $this->getHighestRow();
        $maxCol = Cell::columnIndexFromString($maxCol);

        $rangeBlocks = explode(' ', $range);
        foreach ($rangeBlocks as &$rangeSet) {
            $rangeBoundaries = Cell::getRangeBoundaries($rangeSet);

            if (Cell::columnIndexFromString($rangeBoundaries[0][0]) > $maxCol) {
                $rangeBoundaries[0][0] = Cell::stringFromColumnIndex($maxCol);
            }
            if ($rangeBoundaries[0][1] > $maxRow) {
                $rangeBoundaries[0][1] = $maxRow;
            }
            if (Cell::columnIndexFromString($rangeBoundaries[1][0]) > $maxCol) {
                $rangeBoundaries[1][0] = Cell::stringFromColumnIndex($maxCol);
            }
            if ($rangeBoundaries[1][1] > $maxRow) {
                $rangeBoundaries[1][1] = $maxRow;
            }
            $rangeSet = $rangeBoundaries[0][0] . $rangeBoundaries[0][1] . ':' . $rangeBoundaries[1][0] . $rangeBoundaries[1][1];
        }
        unset($rangeSet);
        $stRange = implode(' ', $rangeBlocks);

        return $stRange;
    }

    /**
     * Get tab color.
     *
     * @return Style\Color
     */
    public function getTabColor()
    {
        if ($this->tabColor === null) {
            $this->tabColor = new Style\Color();
        }

        return $this->tabColor;
    }

    /**
     * Reset tab color.
     *
     * @return Worksheet
     */
    public function resetTabColor()
    {
        $this->tabColor = null;
        unset($this->tabColor);

        return $this;
    }

    /**
     * Tab color set?
     *
     * @return bool
     */
    public function isTabColorSet()
    {
        return $this->tabColor !== null;
    }

    /**
     * Copy worksheet (!= clone!).
     *
     * @return Worksheet
     */
    public function copy()
    {
        $copied = clone $this;

        return $copied;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        foreach ($this as $key => $val) {
            if ($key == 'parent') {
                continue;
            }

            if (is_object($val) || (is_array($val))) {
                if ($key == 'cellCollection') {
                    $newCollection = clone $this->cellCollection;
                    $newCollection->copyCellCollection($this);
                    $this->cellCollection = $newCollection;
                } elseif ($key == 'drawingCollection') {
                    $newCollection = clone $this->drawingCollection;
                    $this->drawingCollection = $newCollection;
                } elseif (($key == 'autoFilter') && ($this->autoFilter instanceof Worksheet\AutoFilter)) {
                    $newAutoFilter = clone $this->autoFilter;
                    $this->autoFilter = $newAutoFilter;
                    $this->autoFilter->setParent($this);
                } else {
                    $this->{$key} = unserialize(serialize($val));
                }
            }
        }
    }

    /**
     * Define the code name of the sheet.
     *
     * @param null|string Same rule as Title minus space not allowed (but, like Excel, change silently space to underscore)
     * @param null|mixed $pValue
     *
     * @throws Exception
     *
     * @return objWorksheet
     */
    public function setCodeName($pValue = null)
    {
        // Is this a 'rename' or not?
        if ($this->getCodeName() == $pValue) {
            return $this;
        }
        $pValue = str_replace(' ', '_', $pValue); //Excel does this automatically without flinching, we are doing the same
        // Syntax check
        // throw an exception if not valid
        self::checkSheetCodeName($pValue);

        // We use the same code that setTitle to find a valid codeName else not using a space (Excel don't like) but a '_'

        if ($this->getParent()) {
            // Is there already such sheet name?
            if ($this->getParent()->sheetCodeNameExists($pValue)) {
                // Use name, but append with lowest possible integer

                if (Shared\StringHelper::countCharacters($pValue) > 29) {
                    $pValue = Shared\StringHelper::substring($pValue, 0, 29);
                }
                $i = 1;
                while ($this->getParent()->sheetCodeNameExists($pValue . '_' . $i)) {
                    ++$i;
                    if ($i == 10) {
                        if (Shared\StringHelper::countCharacters($pValue) > 28) {
                            $pValue = Shared\StringHelper::substring($pValue, 0, 28);
                        }
                    } elseif ($i == 100) {
                        if (Shared\StringHelper::countCharacters($pValue) > 27) {
                            $pValue = Shared\StringHelper::substring($pValue, 0, 27);
                        }
                    }
                }

                $pValue = $pValue . '_' . $i; // ok, we have a valid name
                //codeName is'nt used in formula : no need to call for an update
                //return $this->setTitle($altTitle, $updateFormulaCellReferences);
            }
        }

        $this->codeName = $pValue;

        return $this;
    }

    /**
     * Return the code name of the sheet.
     *
     * @return null|string
     */
    public function getCodeName()
    {
        return $this->codeName;
    }

    /**
     * Sheet has a code name ?
     *
     * @return bool
     */
    public function hasCodeName()
    {
        return !(is_null($this->codeName));
    }
}
