<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use ArrayObject;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Collection\CellsFactory;
use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IComparable;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;

class Worksheet implements IComparable
{
    // Break types
    const BREAK_NONE = 0;
    const BREAK_ROW = 1;
    const BREAK_COLUMN = 2;

    // Sheet state
    const SHEETSTATE_VISIBLE = 'visible';
    const SHEETSTATE_HIDDEN = 'hidden';
    const SHEETSTATE_VERYHIDDEN = 'veryHidden';

    /**
     * Maximum 31 characters allowed for sheet title.
     *
     * @var int
     */
    const SHEET_TITLE_MAXIMUM_LENGTH = 31;

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
     * Collection of cells.
     *
     * @var Cells
     */
    private $cellCollection;

    /**
     * Collection of row dimensions.
     *
     * @var RowDimension[]
     */
    private $rowDimensions = [];

    /**
     * Default row dimension.
     *
     * @var RowDimension
     */
    private $defaultRowDimension;

    /**
     * Collection of column dimensions.
     *
     * @var ColumnDimension[]
     */
    private $columnDimensions = [];

    /**
     * Default column dimension.
     *
     * @var ColumnDimension
     */
    private $defaultColumnDimension;

    /**
     * Collection of drawings.
     *
     * @var BaseDrawing[]
     */
    private $drawingCollection;

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
     * @var PageSetup
     */
    private $pageSetup;

    /**
     * Page margins.
     *
     * @var PageMargins
     */
    private $pageMargins;

    /**
     * Page header/footer.
     *
     * @var HeaderFooter
     */
    private $headerFooter;

    /**
     * Sheet view.
     *
     * @var SheetView
     */
    private $sheetView;

    /**
     * Protection.
     *
     * @var Protection
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
     * @var AutoFilter
     */
    private $autoFilter;

    /**
     * Freeze pane.
     *
     * @var null|string
     */
    private $freezePane;

    /**
     * Default position of the right bottom pane.
     *
     * @var null|string
     */
    private $topLeftCell;

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
     * @var Color
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
    private $codeName;

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

        $this->cellCollection = CellsFactory::getInstance($this);
        // Set page setup
        $this->pageSetup = new PageSetup();
        // Set page margins
        $this->pageMargins = new PageMargins();
        // Set page header/footer
        $this->headerFooter = new HeaderFooter();
        // Set sheet view
        $this->sheetView = new SheetView();
        // Drawing collection
        $this->drawingCollection = new \ArrayObject();
        // Chart collection
        $this->chartCollection = new \ArrayObject();
        // Protection
        $this->protection = new Protection();
        // Default row dimension
        $this->defaultRowDimension = new RowDimension(null);
        // Default column dimension
        $this->defaultColumnDimension = new ColumnDimension(null);
        $this->autoFilter = new AutoFilter(null, $this);
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
     * Return the cell collection.
     *
     * @return Cells
     */
    public function getCellCollection()
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

        // Enforce maximum characters allowed for sheet title
        if ($CharCount > self::SHEET_TITLE_MAXIMUM_LENGTH) {
            throw new Exception('Maximum ' . self::SHEET_TITLE_MAXIMUM_LENGTH . ' characters allowed in sheet code name.');
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

        // Enforce maximum characters allowed for sheet title
        if (Shared\StringHelper::countCharacters($pValue) > self::SHEET_TITLE_MAXIMUM_LENGTH) {
            throw new Exception('Maximum ' . self::SHEET_TITLE_MAXIMUM_LENGTH . ' characters allowed in sheet title.');
        }

        return $pValue;
    }

    /**
     * Get a sorted list of all cell coordinates currently held in the collection by row and column.
     *
     * @param bool $sorted Also sort the cell collection?
     *
     * @return string[]
     */
    public function getCoordinates($sorted = true)
    {
        if ($this->cellCollection == null) {
            return [];
        }

        if ($sorted) {
            return $this->cellCollection->getSortedCoordinates();
        }

        return $this->cellCollection->getCoordinates();
    }

    /**
     * Get collection of row dimensions.
     *
     * @return RowDimension[]
     */
    public function getRowDimensions()
    {
        return $this->rowDimensions;
    }

    /**
     * Get default row dimension.
     *
     * @return RowDimension
     */
    public function getDefaultRowDimension()
    {
        return $this->defaultRowDimension;
    }

    /**
     * Get collection of column dimensions.
     *
     * @return ColumnDimension[]
     */
    public function getColumnDimensions()
    {
        return $this->columnDimensions;
    }

    /**
     * Get default column dimension.
     *
     * @return ColumnDimension
     */
    public function getDefaultColumnDimension()
    {
        return $this->defaultColumnDimension;
    }

    /**
     * Get collection of drawings.
     *
     * @return BaseDrawing[]
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
     * @param null|int $iChartIndex Index where chart should go (0,1,..., or null for last)
     *
     * @return Chart
     */
    public function addChart(Chart $pChart, $iChartIndex = null)
    {
        $pChart->setWorksheet($this);
        if ($iChartIndex === null) {
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
     * @return Chart|false
     */
    public function getChartByIndex($index)
    {
        $chartCount = count($this->chartCollection);
        if ($chartCount == 0) {
            return false;
        }
        if ($index === null) {
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
     * @return Chart|false
     */
    public function getChartByName($chartName)
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
        return 'A1:' . $this->getHighestColumn() . $this->getHighestRow();
    }

    /**
     * Calculate worksheet data dimension.
     *
     * @return string String containing the dimension of this worksheet that actually contain data
     */
    public function calculateWorksheetDataDimension()
    {
        // Return
        return 'A1:' . $this->getHighestDataColumn() . $this->getHighestDataRow();
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
                foreach (Coordinate::extractAllCellReferencesInRange($cells) as $cellReference) {
                    $isMergeCell[$cellReference] = true;
                }
            }

            // loop through all cells in the worksheet
            foreach ($this->getCoordinates(false) as $coordinate) {
                $cell = $this->getCell($coordinate, false);
                if ($cell !== null && isset($autoSizes[$this->cellCollection->getCurrentColumn()])) {
                    //Determine if cell is in merge range
                    $isMerged = isset($isMergeCell[$this->cellCollection->getCurrentCoordinate()]);

                    //By default merged cells should be ignored
                    $isMergedButProceed = false;

                    //The only exception is if it's a merge range value cell of a 'vertical' randge (1 column wide)
                    if ($isMerged && $cell->isMergeRangeValueCell()) {
                        $range = $cell->getMergeRange();
                        $rangeBoundaries = Coordinate::rangeDimension($range);
                        if ($rangeBoundaries[0] == 1) {
                            $isMergedButProceed = true;
                        }
                    }

                    // Determine width if cell does not participate in a merge or does and is a value cell of 1-column wide range
                    if (!$isMerged || $isMergedButProceed) {
                        // Calculated value
                        // To formatted string
                        $cellValue = NumberFormat::toFormattedString(
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
     * @param bool $updateFormulaCellReferences Flag indicating whether cell references in formulae should
     *            be updated to reflect the new sheet name.
     *          This should be left as the default true, unless you are
     *          certain that no formula cells on any worksheet contain
     *          references to this worksheet
     * @param bool $validate False to skip validation of new title. WARNING: This should only be set
     *                       at parse time (by Readers), where titles can be assumed to be valid.
     *
     * @return Worksheet
     */
    public function setTitle($pValue, $updateFormulaCellReferences = true, $validate = true)
    {
        // Is this a 'rename' or not?
        if ($this->getTitle() == $pValue) {
            return $this;
        }

        // Old title
        $oldTitle = $this->getTitle();

        if ($validate) {
            // Syntax check
            self::checkSheetTitle($pValue);

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

                    $pValue .= " $i";
                }
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
    public function setSheetState($value)
    {
        $this->sheetState = $value;

        return $this;
    }

    /**
     * Get page setup.
     *
     * @return PageSetup
     */
    public function getPageSetup()
    {
        return $this->pageSetup;
    }

    /**
     * Set page setup.
     *
     * @param PageSetup $pValue
     *
     * @return Worksheet
     */
    public function setPageSetup(PageSetup $pValue)
    {
        $this->pageSetup = $pValue;

        return $this;
    }

    /**
     * Get page margins.
     *
     * @return PageMargins
     */
    public function getPageMargins()
    {
        return $this->pageMargins;
    }

    /**
     * Set page margins.
     *
     * @param PageMargins $pValue
     *
     * @return Worksheet
     */
    public function setPageMargins(PageMargins $pValue)
    {
        $this->pageMargins = $pValue;

        return $this;
    }

    /**
     * Get page header/footer.
     *
     * @return HeaderFooter
     */
    public function getHeaderFooter()
    {
        return $this->headerFooter;
    }

    /**
     * Set page header/footer.
     *
     * @param HeaderFooter $pValue
     *
     * @return Worksheet
     */
    public function setHeaderFooter(HeaderFooter $pValue)
    {
        $this->headerFooter = $pValue;

        return $this;
    }

    /**
     * Get sheet view.
     *
     * @return SheetView
     */
    public function getSheetView()
    {
        return $this->sheetView;
    }

    /**
     * Set sheet view.
     *
     * @param SheetView $pValue
     *
     * @return Worksheet
     */
    public function setSheetView(SheetView $pValue)
    {
        $this->sheetView = $pValue;

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
     * Set Protection.
     *
     * @param Protection $pValue
     *
     * @return Worksheet
     */
    public function setProtection(Protection $pValue)
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
     * @return int Highest row number that contains data
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
     * @param string $pCoordinate Coordinate of the cell, eg: 'A1'
     * @param mixed $pValue Value of the cell
     *
     * @return Worksheet
     */
    public function setCellValue($pCoordinate, $pValue)
    {
        $this->getCell($pCoordinate)->setValue($pValue);

        return $this;
    }

    /**
     * Set a cell value by using numeric cell coordinates.
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     * @param mixed $value Value of the cell
     *
     * @return Worksheet
     */
    public function setCellValueByColumnAndRow($columnIndex, $row, $value)
    {
        $this->getCellByColumnAndRow($columnIndex, $row)->setValue($value);

        return $this;
    }

    /**
     * Set a cell value.
     *
     * @param string $pCoordinate Coordinate of the cell, eg: 'A1'
     * @param mixed $pValue Value of the cell
     * @param string $pDataType Explicit data type, see DataType::TYPE_*
     *
     * @return Worksheet
     */
    public function setCellValueExplicit($pCoordinate, $pValue, $pDataType)
    {
        // Set value
        $this->getCell($pCoordinate)->setValueExplicit($pValue, $pDataType);

        return $this;
    }

    /**
     * Set a cell value by using numeric cell coordinates.
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     * @param mixed $value Value of the cell
     * @param string $dataType Explicit data type, see DataType::TYPE_*
     *
     * @return Worksheet
     */
    public function setCellValueExplicitByColumnAndRow($columnIndex, $row, $value, $dataType)
    {
        $this->getCellByColumnAndRow($columnIndex, $row)->setValueExplicit($value, $dataType);

        return $this;
    }

    /**
     * Get cell at a specific coordinate.
     *
     * @param string $pCoordinate Coordinate of the cell, eg: 'A1'
     * @param bool $createIfNotExists Flag indicating whether a new cell should be created if it doesn't
     *                                       already exist, or a null should be returned instead
     *
     * @throws Exception
     *
     * @return null|Cell Cell that was found/created or null
     */
    public function getCell($pCoordinate, $createIfNotExists = true)
    {
        // Uppercase coordinate
        $pCoordinateUpper = strtoupper($pCoordinate);

        // Check cell collection
        if ($this->cellCollection->has($pCoordinateUpper)) {
            return $this->cellCollection->get($pCoordinateUpper);
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

        if (Coordinate::coordinateIsRange($pCoordinate)) {
            throw new Exception('Cell coordinate can not be a range of cells.');
        } elseif (strpos($pCoordinate, '$') !== false) {
            throw new Exception('Cell coordinate must not be absolute.');
        }

        // Create new cell object, if required
        return $createIfNotExists ? $this->createNewCell($pCoordinateUpper) : null;
    }

    /**
     * Get cell at a specific coordinate by using numeric cell coordinates.
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     * @param bool $createIfNotExists Flag indicating whether a new cell should be created if it doesn't
     *                                       already exist, or a null should be returned instead
     *
     * @return null|Cell Cell that was found/created or null
     */
    public function getCellByColumnAndRow($columnIndex, $row, $createIfNotExists = true)
    {
        $columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
        $coordinate = $columnLetter . $row;

        if ($this->cellCollection->has($coordinate)) {
            return $this->cellCollection->get($coordinate);
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
        $cell = new Cell(null, DataType::TYPE_NULL, $this);
        $this->cellCollection->add($pCoordinate, $cell);
        $this->cellCollectionIsSorted = false;

        // Coordinates
        $aCoordinates = Coordinate::coordinateFromString($pCoordinate);
        if (Coordinate::columnIndexFromString($this->cachedHighestColumn) < Coordinate::columnIndexFromString($aCoordinates[0])) {
            $this->cachedHighestColumn = $aCoordinates[0];
        }
        if ($aCoordinates[1] > $this->cachedHighestRow) {
            $this->cachedHighestRow = $aCoordinates[1];
        }

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
     * @param string $pCoordinate Coordinate of the cell eg: 'A1'
     *
     * @throws Exception
     *
     * @return bool
     */
    public function cellExists($pCoordinate)
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

        if (Coordinate::coordinateIsRange($pCoordinate)) {
            throw new Exception('Cell coordinate can not be a range of cells.');
        } elseif (strpos($pCoordinate, '$') !== false) {
            throw new Exception('Cell coordinate must not be absolute.');
        }

        // Cell exists?
        return $this->cellCollection->has($pCoordinate);
    }

    /**
     * Cell at a specific coordinate by using numeric cell coordinates exists?
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     *
     * @return bool
     */
    public function cellExistsByColumnAndRow($columnIndex, $row)
    {
        return $this->cellExists(Coordinate::stringFromColumnIndex($columnIndex) . $row);
    }

    /**
     * Get row dimension at a specific row.
     *
     * @param int $pRow Numeric index of the row
     * @param bool $create
     *
     * @return RowDimension
     */
    public function getRowDimension($pRow, $create = true)
    {
        // Found
        $found = null;

        // Get row dimension
        if (!isset($this->rowDimensions[$pRow])) {
            if (!$create) {
                return null;
            }
            $this->rowDimensions[$pRow] = new RowDimension($pRow);

            $this->cachedHighestRow = max($this->cachedHighestRow, $pRow);
        }

        return $this->rowDimensions[$pRow];
    }

    /**
     * Get column dimension at a specific column.
     *
     * @param string $pColumn String index of the column eg: 'A'
     * @param bool $create
     *
     * @return ColumnDimension
     */
    public function getColumnDimension($pColumn, $create = true)
    {
        // Uppercase coordinate
        $pColumn = strtoupper($pColumn);

        // Fetch dimensions
        if (!isset($this->columnDimensions[$pColumn])) {
            if (!$create) {
                return null;
            }
            $this->columnDimensions[$pColumn] = new ColumnDimension($pColumn);

            if (Coordinate::columnIndexFromString($this->cachedHighestColumn) < Coordinate::columnIndexFromString($pColumn)) {
                $this->cachedHighestColumn = $pColumn;
            }
        }

        return $this->columnDimensions[$pColumn];
    }

    /**
     * Get column dimension at a specific column by using numeric cell coordinates.
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     *
     * @return ColumnDimension
     */
    public function getColumnDimensionByColumn($columnIndex)
    {
        return $this->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex));
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
     * @param string $pCellCoordinate Cell coordinate (or range) to get style for, eg: 'A1'
     *
     * @throws Exception
     *
     * @return Style
     */
    public function getStyle($pCellCoordinate)
    {
        // set this sheet as active
        $this->parent->setActiveSheetIndex($this->parent->getIndex($this));

        // set cell coordinate as active
        $this->setSelectedCells($pCellCoordinate);

        return $this->parent->getCellXfSupervisor();
    }

    /**
     * Get conditional styles for a cell.
     *
     * @param string $pCoordinate eg: 'A1'
     *
     * @return Conditional[]
     */
    public function getConditionalStyles($pCoordinate)
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
     * @param string $pCoordinate eg: 'A1'
     *
     * @return bool
     */
    public function conditionalStylesExists($pCoordinate)
    {
        return isset($this->conditionalStylesCollection[strtoupper($pCoordinate)]);
    }

    /**
     * Removes conditional styles for a cell.
     *
     * @param string $pCoordinate eg: 'A1'
     *
     * @return Worksheet
     */
    public function removeConditionalStyles($pCoordinate)
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
     * @param $pValue Conditional[]
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
     * @param int $columnIndex1 Numeric column coordinate of the cell
     * @param int $row1 Numeric row coordinate of the cell
     * @param null|int $columnIndex2 Numeric column coordinate of the range cell
     * @param null|int $row2 Numeric row coordinate of the range cell
     *
     * @return Style
     */
    public function getStyleByColumnAndRow($columnIndex1, $row1, $columnIndex2 = null, $row2 = null)
    {
        if ($columnIndex2 !== null && $row2 !== null) {
            $cellRange = Coordinate::stringFromColumnIndex($columnIndex1) . $row1 . ':' . Coordinate::stringFromColumnIndex($columnIndex2) . $row2;

            return $this->getStyle($cellRange);
        }

        return $this->getStyle(Coordinate::stringFromColumnIndex($columnIndex1) . $row1);
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
    public function duplicateStyle(Style $pCellStyle, $pRange)
    {
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
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($pRange . ':' . $pRange);

        // Make sure we can loop upwards on rows and columns
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd = $tmp;
        }

        // Loop through cells and apply styles
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->getCell(Coordinate::stringFromColumnIndex($col) . $row)->setXfIndex($xfIndex);
            }
        }

        return $this;
    }

    /**
     * Duplicate conditional style to a range of cells.
     *
     * Please note that this will overwrite existing cell styles for cells in range!
     *
     * @param Conditional[] $pCellStyle Cell style to duplicate
     * @param string $pRange Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function duplicateConditionalStyle(array $pCellStyle, $pRange = '')
    {
        foreach ($pCellStyle as $cellStyle) {
            if (!($cellStyle instanceof Conditional)) {
                throw new Exception('Style is not a conditional style');
            }
        }

        // Calculate range outer borders
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($pRange . ':' . $pRange);

        // Make sure we can loop upwards on rows and columns
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd = $tmp;
        }

        // Loop through cells and apply styles
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->setConditionalStyles(Coordinate::stringFromColumnIndex($col) . $row, $pCellStyle);
            }
        }

        return $this;
    }

    /**
     * Set break on a cell.
     *
     * @param string $pCoordinate Cell coordinate (e.g. A1)
     * @param int $pBreak Break type (type of Worksheet::BREAK_*)
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setBreak($pCoordinate, $pBreak)
    {
        // Uppercase coordinate
        $pCoordinate = strtoupper($pCoordinate);

        if ($pCoordinate != '') {
            if ($pBreak == self::BREAK_NONE) {
                if (isset($this->breaks[$pCoordinate])) {
                    unset($this->breaks[$pCoordinate]);
                }
            } else {
                $this->breaks[$pCoordinate] = $pBreak;
            }
        } else {
            throw new Exception('No cell coordinate specified.');
        }

        return $this;
    }

    /**
     * Set break on a cell by using numeric cell coordinates.
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     * @param int $break Break type (type of Worksheet::BREAK_*)
     *
     * @return Worksheet
     */
    public function setBreakByColumnAndRow($columnIndex, $row, $break)
    {
        return $this->setBreak(Coordinate::stringFromColumnIndex($columnIndex) . $row, $break);
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
    public function mergeCells($pRange)
    {
        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        if (strpos($pRange, ':') !== false) {
            $this->mergeCells[$pRange] = $pRange;

            // make sure cells are created

            // get the cells in the range
            $aReferences = Coordinate::extractAllCellReferencesInRange($pRange);

            // create upper left cell if it does not already exist
            $upperLeft = $aReferences[0];
            if (!$this->cellExists($upperLeft)) {
                $this->getCell($upperLeft)->setValueExplicit(null, DataType::TYPE_NULL);
            }

            // Blank out the rest of the cells in the range (if they exist)
            $count = count($aReferences);
            for ($i = 1; $i < $count; ++$i) {
                if ($this->cellExists($aReferences[$i])) {
                    $this->getCell($aReferences[$i])->setValueExplicit(null, DataType::TYPE_NULL);
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
     * @param int $columnIndex1 Numeric column coordinate of the first cell
     * @param int $row1 Numeric row coordinate of the first cell
     * @param int $columnIndex2 Numeric column coordinate of the last cell
     * @param int $row2 Numeric row coordinate of the last cell
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function mergeCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2)
    {
        $cellRange = Coordinate::stringFromColumnIndex($columnIndex1) . $row1 . ':' . Coordinate::stringFromColumnIndex($columnIndex2) . $row2;

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
    public function unmergeCells($pRange)
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
     * @param int $columnIndex1 Numeric column coordinate of the first cell
     * @param int $row1 Numeric row coordinate of the first cell
     * @param int $columnIndex2 Numeric column coordinate of the last cell
     * @param int $row2 Numeric row coordinate of the last cell
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function unmergeCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2)
    {
        $cellRange = Coordinate::stringFromColumnIndex($columnIndex1) . $row1 . ':' . Coordinate::stringFromColumnIndex($columnIndex2) . $row2;

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
     * @param array $pValue
     *
     * @return Worksheet
     */
    public function setMergeCells(array $pValue)
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
     * @return Worksheet
     */
    public function protectCells($pRange, $pPassword, $pAlreadyHashed = false)
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
     * @param int $columnIndex1 Numeric column coordinate of the first cell
     * @param int $row1 Numeric row coordinate of the first cell
     * @param int $columnIndex2 Numeric column coordinate of the last cell
     * @param int $row2 Numeric row coordinate of the last cell
     * @param string $password Password to unlock the protection
     * @param bool $alreadyHashed If the password has already been hashed, set this to true
     *
     * @return Worksheet
     */
    public function protectCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2, $password, $alreadyHashed = false)
    {
        $cellRange = Coordinate::stringFromColumnIndex($columnIndex1) . $row1 . ':' . Coordinate::stringFromColumnIndex($columnIndex2) . $row2;

        return $this->protectCells($cellRange, $password, $alreadyHashed);
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
    public function unprotectCells($pRange)
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
     * @param int $columnIndex1 Numeric column coordinate of the first cell
     * @param int $row1 Numeric row coordinate of the first cell
     * @param int $columnIndex2 Numeric column coordinate of the last cell
     * @param int $row2 Numeric row coordinate of the last cell
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function unprotectCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2)
    {
        $cellRange = Coordinate::stringFromColumnIndex($columnIndex1) . $row1 . ':' . Coordinate::stringFromColumnIndex($columnIndex2) . $row2;

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
     * @return AutoFilter
     */
    public function getAutoFilter()
    {
        return $this->autoFilter;
    }

    /**
     * Set AutoFilter.
     *
     * @param AutoFilter|string $pValue
     *            A simple string containing a Cell range like 'A1:E10' is permitted for backward compatibility
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setAutoFilter($pValue)
    {
        if (is_string($pValue)) {
            $this->autoFilter->setRange($pValue);
        } elseif (is_object($pValue) && ($pValue instanceof AutoFilter)) {
            $this->autoFilter = $pValue;
        }

        return $this;
    }

    /**
     * Set Autofilter Range by using numeric cell coordinates.
     *
     * @param int $columnIndex1 Numeric column coordinate of the first cell
     * @param int $row1 Numeric row coordinate of the first cell
     * @param int $columnIndex2 Numeric column coordinate of the second cell
     * @param int $row2 Numeric row coordinate of the second cell
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setAutoFilterByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2)
    {
        return $this->setAutoFilter(
            Coordinate::stringFromColumnIndex($columnIndex1) . $row1
            . ':' .
            Coordinate::stringFromColumnIndex($columnIndex2) . $row2
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
     * Examples:
     *
     *     - A2 will freeze the rows above cell A2 (i.e row 1)
     *     - B1 will freeze the columns to the left of cell B1 (i.e column A)
     *     - B2 will freeze the rows above and to the left of cell B2 (i.e row 1 and column A)
     *
     * @param null|string $cell Position of the split
     * @param null|string $topLeftCell default position of the right bottom pane
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function freezePane($cell, $topLeftCell = null)
    {
        if (is_string($cell) && Coordinate::coordinateIsRange($cell)) {
            throw new Exception('Freeze pane can not be set on a range of cells.');
        }

        if ($cell !== null && $topLeftCell === null) {
            $coordinate = Coordinate::coordinateFromString($cell);
            $topLeftCell = $coordinate[0] . $coordinate[1];
        }

        $this->freezePane = $cell;
        $this->topLeftCell = $topLeftCell;

        return $this;
    }

    /**
     * Freeze Pane by using numeric cell coordinates.
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     *
     * @return Worksheet
     */
    public function freezePaneByColumnAndRow($columnIndex, $row)
    {
        return $this->freezePane(Coordinate::stringFromColumnIndex($columnIndex) . $row);
    }

    /**
     * Unfreeze Pane.
     *
     * @return Worksheet
     */
    public function unfreezePane()
    {
        return $this->freezePane(null);
    }

    /**
     * Get the default position of the right bottom pane.
     *
     * @return int
     */
    public function getTopLeftCell()
    {
        return $this->topLeftCell;
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
    public function insertNewRowBefore($pBefore, $pNumRows = 1)
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
     * @param string $pBefore Insert before this one, eg: 'A'
     * @param int $pNumCols Number of columns to insert
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function insertNewColumnBefore($pBefore, $pNumCols = 1)
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
     * @param int $beforeColumnIndex Insert before this one (numeric column coordinate of the cell)
     * @param int $pNumCols Number of columns to insert
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function insertNewColumnBeforeByIndex($beforeColumnIndex, $pNumCols = 1)
    {
        if ($beforeColumnIndex >= 1) {
            return $this->insertNewColumnBefore(Coordinate::stringFromColumnIndex($beforeColumnIndex), $pNumCols);
        }

        throw new Exception('Columns can only be inserted before at least column A (1).');
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
    public function removeRow($pRow, $pNumRows = 1)
    {
        if ($pRow >= 1) {
            for ($r = 0; $r < $pNumRows; ++$r) {
                $this->getCellCollection()->removeRow($pRow + $r);
            }

            $highestRow = $this->getHighestDataRow();
            $objReferenceHelper = ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore('A' . ($pRow + $pNumRows), 0, -$pNumRows, $this);
            for ($r = 0; $r < $pNumRows; ++$r) {
                $this->getCellCollection()->removeRow($highestRow);
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
     * @param string $pColumn Remove starting with this one, eg: 'A'
     * @param int $pNumCols Number of columns to remove
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function removeColumn($pColumn, $pNumCols = 1)
    {
        if (is_numeric($pColumn)) {
            throw new Exception('Column references should not be numeric.');
        }

        $highestColumn = $this->getHighestDataColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $pColumnIndex = Coordinate::columnIndexFromString($pColumn);

        if ($pColumnIndex > $highestColumnIndex) {
            return $this;
        }

        $pColumn = Coordinate::stringFromColumnIndex($pColumnIndex + $pNumCols);
        $objReferenceHelper = ReferenceHelper::getInstance();
        $objReferenceHelper->insertNewBefore($pColumn . '1', -$pNumCols, 0, $this);

        $maxPossibleColumnsToBeRemoved = $highestColumnIndex - $pColumnIndex + 1;

        for ($c = 0, $n = min($maxPossibleColumnsToBeRemoved, $pNumCols); $c < $n; ++$c) {
            $this->getCellCollection()->removeColumn($highestColumn);
            $highestColumn = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($highestColumn) - 1);
        }

        $this->garbageCollect();

        return $this;
    }

    /**
     * Remove a column, updating all possible related data.
     *
     * @param int $columnIndex Remove starting with this one (numeric column coordinate of the cell)
     * @param int $numColumns Number of columns to remove
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function removeColumnByIndex($columnIndex, $numColumns = 1)
    {
        if ($columnIndex >= 1) {
            return $this->removeColumn(Coordinate::stringFromColumnIndex($columnIndex), $numColumns);
        }

        throw new Exception('Columns to be deleted should at least start from column A (1)');
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
    public function setShowGridlines($pValue)
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
    public function setPrintGridlines($pValue)
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
    public function setShowRowColHeaders($pValue)
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
    public function setShowSummaryBelow($pValue)
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
    public function setShowSummaryRight($pValue)
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
     * @param Comment[] $pValue
     *
     * @return Worksheet
     */
    public function setComments(array $pValue)
    {
        $this->comments = $pValue;

        return $this;
    }

    /**
     * Get comment for cell.
     *
     * @param string $pCellCoordinate Cell coordinate to get comment for, eg: 'A1'
     *
     * @throws Exception
     *
     * @return Comment
     */
    public function getComment($pCellCoordinate)
    {
        // Uppercase coordinate
        $pCellCoordinate = strtoupper($pCellCoordinate);

        if (Coordinate::coordinateIsRange($pCellCoordinate)) {
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
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     *
     * @return Comment
     */
    public function getCommentByColumnAndRow($columnIndex, $row)
    {
        return $this->getComment(Coordinate::stringFromColumnIndex($columnIndex) . $row);
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
    public function setSelectedCell($pCoordinate)
    {
        return $this->setSelectedCells($pCoordinate);
    }

    /**
     * Select a range of cells.
     *
     * @param string $pCoordinate Cell range, examples: 'A1', 'B2:G5', 'A:C', '3:6'
     *
     * @return Worksheet
     */
    public function setSelectedCells($pCoordinate)
    {
        // Uppercase coordinate
        $pCoordinate = strtoupper($pCoordinate);

        // Convert 'A' to 'A:A'
        $pCoordinate = preg_replace('/^([A-Z]+)$/', '${1}:${1}', $pCoordinate);

        // Convert '1' to '1:1'
        $pCoordinate = preg_replace('/^(\d+)$/', '${1}:${1}', $pCoordinate);

        // Convert 'A:C' to 'A1:C1048576'
        $pCoordinate = preg_replace('/^([A-Z]+):([A-Z]+)$/', '${1}1:${2}1048576', $pCoordinate);

        // Convert '1:3' to 'A1:XFD3'
        $pCoordinate = preg_replace('/^(\d+):(\d+)$/', 'A${1}:XFD${2}', $pCoordinate);

        if (Coordinate::coordinateIsRange($pCoordinate)) {
            [$first] = Coordinate::splitRange($pCoordinate);
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
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setSelectedCellByColumnAndRow($columnIndex, $row)
    {
        return $this->setSelectedCells(Coordinate::stringFromColumnIndex($columnIndex) . $row);
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
    public function setRightToLeft($value)
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
    public function fromArray(array $source, $nullValue = null, $startCell = 'A1', $strictNullComparison = false)
    {
        //    Convert a 1-D array to 2-D (for ease of looping)
        if (!is_array(end($source))) {
            $source = [$source];
        }

        // start coordinate
        [$startColumn, $startRow] = Coordinate::coordinateFromString($startCell);

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
    public function rangeToArray($pRange, $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
    {
        // Returnvalue
        $returnValue = [];
        //    Identify the range that we need to extract from the worksheet
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($pRange);
        $minCol = Coordinate::stringFromColumnIndex($rangeStart[0]);
        $minRow = $rangeStart[1];
        $maxCol = Coordinate::stringFromColumnIndex($rangeEnd[0]);
        $maxRow = $rangeEnd[1];

        ++$maxCol;
        // Loop through rows
        $r = -1;
        for ($row = $minRow; $row <= $maxRow; ++$row) {
            $rRef = $returnCellRef ? $row : ++$r;
            $c = -1;
            // Loop through columns in the current row
            for ($col = $minCol; $col != $maxCol; ++$col) {
                $cRef = $returnCellRef ? $col : ++$c;
                //    Using getCell() will create a new cell if it doesn't already exist. We don't want that to happen
                //        so we test and retrieve directly against cellCollection
                if ($this->cellCollection->has($col . $row)) {
                    // Cell exists
                    $cell = $this->cellCollection->get($col . $row);
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
                            $returnValue[$rRef][$cRef] = NumberFormat::toFormattedString(
                                $returnValue[$rRef][$cRef],
                                ($style && $style->getNumberFormat()) ? $style->getNumberFormat()->getFormatCode() : NumberFormat::FORMAT_GENERAL
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
    public function namedRangeToArray($pNamedRange, $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)
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
     * @return RowIterator
     */
    public function getRowIterator($startRow = 1, $endRow = null)
    {
        return new RowIterator($this, $startRow, $endRow);
    }

    /**
     * Get column iterator.
     *
     * @param string $startColumn The column address at which to start iterating
     * @param string $endColumn The column address at which to stop iterating
     *
     * @return ColumnIterator
     */
    public function getColumnIterator($startColumn = 'A', $endColumn = null)
    {
        return new ColumnIterator($this, $startColumn, $endColumn);
    }

    /**
     * Run PhpSpreadsheet garbage collector.
     *
     * @return Worksheet
     */
    public function garbageCollect()
    {
        // Flush cache
        $this->cellCollection->get('A1');

        // Lookup highest column and highest row if cells are cleaned
        $colRow = $this->cellCollection->getHighestRowAndColumn();
        $highestRow = $colRow['row'];
        $highestColumn = Coordinate::columnIndexFromString($colRow['column']);

        // Loop through column dimensions
        foreach ($this->columnDimensions as $dimension) {
            $highestColumn = max($highestColumn, Coordinate::columnIndexFromString($dimension->getColumnIndex()));
        }

        // Loop through row dimensions
        foreach ($this->rowDimensions as $dimension) {
            $highestRow = max($highestRow, $dimension->getRowIndex());
        }

        // Cache values
        if ($highestColumn < 1) {
            $this->cachedHighestColumn = 'A';
        } else {
            $this->cachedHighestColumn = Coordinate::stringFromColumnIndex($highestColumn);
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
     * Example: extractSheetTitle("'testSheet 1'!A1", true) ==> ['testSheet 1', 'A1'];
     *
     * @param string $pRange Range to extract title from
     * @param bool $returnRange Return range? (see example)
     *
     * @return mixed
     */
    public static function extractSheetTitle($pRange, $returnRange = false)
    {
        // Sheet title included?
        if (($sep = strrpos($pRange, '!')) === false) {
            return $returnRange ? ['', $pRange] : '';
        }

        if ($returnRange) {
            return [substr($pRange, 0, $sep), substr($pRange, $sep + 1)];
        }

        return substr($pRange, $sep + 1);
    }

    /**
     * Get hyperlink.
     *
     * @param string $pCellCoordinate Cell coordinate to get hyperlink for, eg: 'A1'
     *
     * @return Hyperlink
     */
    public function getHyperlink($pCellCoordinate)
    {
        // return hyperlink if we already have one
        if (isset($this->hyperlinkCollection[$pCellCoordinate])) {
            return $this->hyperlinkCollection[$pCellCoordinate];
        }

        // else create hyperlink
        $this->hyperlinkCollection[$pCellCoordinate] = new Hyperlink();

        return $this->hyperlinkCollection[$pCellCoordinate];
    }

    /**
     * Set hyperlink.
     *
     * @param string $pCellCoordinate Cell coordinate to insert hyperlink, eg: 'A1'
     * @param null|Hyperlink $pHyperlink
     *
     * @return Worksheet
     */
    public function setHyperlink($pCellCoordinate, Hyperlink $pHyperlink = null)
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
     * @param string $pCoordinate eg: 'A1'
     *
     * @return bool
     */
    public function hyperlinkExists($pCoordinate)
    {
        return isset($this->hyperlinkCollection[$pCoordinate]);
    }

    /**
     * Get collection of hyperlinks.
     *
     * @return Hyperlink[]
     */
    public function getHyperlinkCollection()
    {
        return $this->hyperlinkCollection;
    }

    /**
     * Get data validation.
     *
     * @param string $pCellCoordinate Cell coordinate to get data validation for, eg: 'A1'
     *
     * @return DataValidation
     */
    public function getDataValidation($pCellCoordinate)
    {
        // return data validation if we already have one
        if (isset($this->dataValidationCollection[$pCellCoordinate])) {
            return $this->dataValidationCollection[$pCellCoordinate];
        }

        // else create data validation
        $this->dataValidationCollection[$pCellCoordinate] = new DataValidation();

        return $this->dataValidationCollection[$pCellCoordinate];
    }

    /**
     * Set data validation.
     *
     * @param string $pCellCoordinate Cell coordinate to insert data validation, eg: 'A1'
     * @param null|DataValidation $pDataValidation
     *
     * @return Worksheet
     */
    public function setDataValidation($pCellCoordinate, DataValidation $pDataValidation = null)
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
     * @param string $pCoordinate eg: 'A1'
     *
     * @return bool
     */
    public function dataValidationExists($pCoordinate)
    {
        return isset($this->dataValidationCollection[$pCoordinate]);
    }

    /**
     * Get collection of data validations.
     *
     * @return DataValidation[]
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
        $maxCol = Coordinate::columnIndexFromString($maxCol);

        $rangeBlocks = explode(' ', $range);
        foreach ($rangeBlocks as &$rangeSet) {
            $rangeBoundaries = Coordinate::getRangeBoundaries($rangeSet);

            if (Coordinate::columnIndexFromString($rangeBoundaries[0][0]) > $maxCol) {
                $rangeBoundaries[0][0] = Coordinate::stringFromColumnIndex($maxCol);
            }
            if ($rangeBoundaries[0][1] > $maxRow) {
                $rangeBoundaries[0][1] = $maxRow;
            }
            if (Coordinate::columnIndexFromString($rangeBoundaries[1][0]) > $maxCol) {
                $rangeBoundaries[1][0] = Coordinate::stringFromColumnIndex($maxCol);
            }
            if ($rangeBoundaries[1][1] > $maxRow) {
                $rangeBoundaries[1][1] = $maxRow;
            }
            $rangeSet = $rangeBoundaries[0][0] . $rangeBoundaries[0][1] . ':' . $rangeBoundaries[1][0] . $rangeBoundaries[1][1];
        }
        unset($rangeSet);

        return implode(' ', $rangeBlocks);
    }

    /**
     * Get tab color.
     *
     * @return Color
     */
    public function getTabColor()
    {
        if ($this->tabColor === null) {
            $this->tabColor = new Color();
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
        return clone $this;
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
                    $newCollection = $this->cellCollection->cloneCellCollection($this);
                    $this->cellCollection = $newCollection;
                } elseif ($key == 'drawingCollection') {
                    $currentCollection = $this->drawingCollection;
                    $this->drawingCollection = new ArrayObject();
                    foreach ($currentCollection as $item) {
                        if (is_object($item)) {
                            $newDrawing = clone $item;
                            $newDrawing->setWorksheet($this);
                        }
                    }
                } elseif (($key == 'autoFilter') && ($this->autoFilter instanceof AutoFilter)) {
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
     * @param string $pValue Same rule as Title minus space not allowed (but, like Excel, change
     *                       silently space to underscore)
     * @param bool $validate False to skip validation of new title. WARNING: This should only be set
     *                       at parse time (by Readers), where titles can be assumed to be valid.
     *
     * @throws Exception
     *
     * @return Worksheet
     */
    public function setCodeName($pValue, $validate = true)
    {
        // Is this a 'rename' or not?
        if ($this->getCodeName() == $pValue) {
            return $this;
        }

        if ($validate) {
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

                    $pValue .= '_' . $i; // ok, we have a valid name
                }
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
        return $this->codeName !== null;
    }
}
