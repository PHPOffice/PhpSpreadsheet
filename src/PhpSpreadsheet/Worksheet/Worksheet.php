<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use ArrayObject;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\CellAddress;
use PhpOffice\PhpSpreadsheet\Cell\CellRange;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Collection\CellsFactory;
use PhpOffice\PhpSpreadsheet\Comment;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IComparable;
use PhpOffice\PhpSpreadsheet\ReferenceHelper;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection as StyleProtection;
use PhpOffice\PhpSpreadsheet\Style\Style;

class Worksheet implements IComparable
{
    // Break types
    public const BREAK_NONE = 0;
    public const BREAK_ROW = 1;
    public const BREAK_COLUMN = 2;
    // Maximum column for row break
    public const BREAK_ROW_MAX_COLUMN = 16383;

    // Sheet state
    public const SHEETSTATE_VISIBLE = 'visible';
    public const SHEETSTATE_HIDDEN = 'hidden';
    public const SHEETSTATE_VERYHIDDEN = 'veryHidden';

    public const MERGE_CELL_CONTENT_EMPTY = 'empty';
    public const MERGE_CELL_CONTENT_HIDE = 'hide';
    public const MERGE_CELL_CONTENT_MERGE = 'merge';

    protected const SHEET_NAME_REQUIRES_NO_QUOTES = '/^[_\p{L}][_\p{L}\p{N}]*$/mui';

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
     */
    private ?Spreadsheet $parent;

    /**
     * Collection of cells.
     */
    private ?Cells $cellCollection;

    /**
     * Collection of row dimensions.
     *
     * @var RowDimension[]
     */
    private $rowDimensions = [];

    /**
     * Default row dimension.
     */
    private RowDimension $defaultRowDimension;

    /**
     * Collection of column dimensions.
     *
     * @var ColumnDimension[]
     */
    private $columnDimensions = [];

    /**
     * Default column dimension.
     */
    private ColumnDimension $defaultColumnDimension;

    /**
     * Collection of drawings.
     *
     * @var ArrayObject<int, BaseDrawing>
     */
    private ArrayObject $drawingCollection;

    /**
     * Collection of Chart objects.
     *
     * @var ArrayObject<int, Chart>
     */
    private ArrayObject $chartCollection;

    /**
     * Collection of Table objects.
     *
     * @var ArrayObject<int, Table>
     */
    private ArrayObject $tableCollection;

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
     */
    private PageSetup $pageSetup;

    /**
     * Page margins.
     */
    private PageMargins $pageMargins;

    /**
     * Page header/footer.
     */
    private HeaderFooter $headerFooter;

    /**
     * Sheet view.
     */
    private SheetView $sheetView;

    /**
     * Protection.
     */
    private Protection $protection;

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
     * Collection of row breaks.
     *
     * @var PageBreak[]
     */
    private $rowBreaks = [];

    /**
     * Collection of column breaks.
     *
     * @var PageBreak[]
     */
    private $columnBreaks = [];

    /**
     * Collection of merged cell ranges.
     *
     * @var string[]
     */
    private $mergeCells = [];

    /**
     * Collection of protected cell ranges.
     *
     * @var string[]
     */
    private $protectedCells = [];

    /**
     * Autofilter Range and selection.
     */
    private AutoFilter $autoFilter;

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

    private string $paneTopLeftCell = '';

    private string $activePane = '';

    private int $xSplit = 0;

    private int $ySplit = 0;

    private string $paneState = '';

    /**
     * Properties of the 4 panes.
     *
     * @var (null|Pane)[]
     */
    private $panes = [
        'bottomRight' => null,
        'bottomLeft' => null,
        'topRight' => null,
        'topLeft' => null,
    ];

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
     * @var int
     */
    private $cachedHighestColumn = 1;

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
     * @var null|Color
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
     * @param string $title
     */
    public function __construct(?Spreadsheet $parent = null, $title = 'Worksheet')
    {
        // Set parent and title
        $this->parent = $parent;
        $this->setTitle($title, false);
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
        $this->drawingCollection = new ArrayObject();
        // Chart collection
        $this->chartCollection = new ArrayObject();
        // Protection
        $this->protection = new Protection();
        // Default row dimension
        $this->defaultRowDimension = new RowDimension(null);
        // Default column dimension
        $this->defaultColumnDimension = new ColumnDimension(null);
        // AutoFilter
        $this->autoFilter = new AutoFilter('', $this);
        // Table collection
        $this->tableCollection = new ArrayObject();
    }

    /**
     * Disconnect all cells from this Worksheet object,
     * typically so that the worksheet object can be unset.
     */
    public function disconnectCells(): void
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
        unset($this->rowDimensions, $this->columnDimensions, $this->tableCollection, $this->drawingCollection, $this->chartCollection, $this->autoFilter);
    }

    /**
     * Return the cell collection.
     *
     * @return Cells
     */
    public function getCellCollection()
    {
        // @phpstan-ignore-next-line
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
     * @param string $sheetCodeName The string to check
     *
     * @return string The valid string
     */
    private static function checkSheetCodeName($sheetCodeName): string
    {
        $charCount = Shared\StringHelper::countCharacters($sheetCodeName);
        if ($charCount == 0) {
            throw new Exception('Sheet code name cannot be empty.');
        }
        // Some of the printable ASCII characters are invalid:  * : / \ ? [ ] and  first and last characters cannot be a "'"
        if (
            (str_replace(self::$invalidCharacters, '', $sheetCodeName) !== $sheetCodeName)
            || (Shared\StringHelper::substring($sheetCodeName, -1, 1) == '\'')
            || (Shared\StringHelper::substring($sheetCodeName, 0, 1) == '\'')
        ) {
            throw new Exception('Invalid character found in sheet code name');
        }

        // Enforce maximum characters allowed for sheet title
        if ($charCount > self::SHEET_TITLE_MAXIMUM_LENGTH) {
            throw new Exception('Maximum ' . self::SHEET_TITLE_MAXIMUM_LENGTH . ' characters allowed in sheet code name.');
        }

        return $sheetCodeName;
    }

    /**
     * Check sheet title for valid Excel syntax.
     *
     * @param string $sheetTitle The string to check
     *
     * @return string The valid string
     */
    private static function checkSheetTitle($sheetTitle): string
    {
        // Some of the printable ASCII characters are invalid:  * : / \ ? [ ]
        if (str_replace(self::$invalidCharacters, '', $sheetTitle) !== $sheetTitle) {
            throw new Exception('Invalid character found in sheet title');
        }

        // Enforce maximum characters allowed for sheet title
        if (Shared\StringHelper::countCharacters($sheetTitle) > self::SHEET_TITLE_MAXIMUM_LENGTH) {
            throw new Exception('Maximum ' . self::SHEET_TITLE_MAXIMUM_LENGTH . ' characters allowed in sheet title.');
        }

        return $sheetTitle;
    }

    /**
     * Get a sorted list of all cell coordinates currently held in the collection by row and column.
     *
     * @param bool $sorted Also sort the cell collection?
     *
     * @return string[]
     */
    public function getCoordinates($sorted = true): array
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
        /** @var callable */
        $callable = [self::class, 'columnDimensionCompare'];
        uasort($this->columnDimensions, $callable);

        return $this->columnDimensions;
    }

    private static function columnDimensionCompare(ColumnDimension $a, ColumnDimension $b): int
    {
        return $a->getColumnNumeric() - $b->getColumnNumeric();
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
     * @return ArrayObject<int, BaseDrawing>
     */
    public function getDrawingCollection()
    {
        return $this->drawingCollection;
    }

    /**
     * Get collection of charts.
     *
     * @return ArrayObject<int, Chart>
     */
    public function getChartCollection()
    {
        return $this->chartCollection;
    }

    public function addChart(Chart $chart): Chart
    {
        $chart->setWorksheet($this);
        $this->chartCollection[] = $chart;

        return $chart;
    }

    /**
     * Return the count of charts on this worksheet.
     *
     * @return int The number of charts
     */
    public function getChartCount(): int
    {
        return count($this->chartCollection);
    }

    /**
     * Get a chart by its index position.
     *
     * @param ?string $index Chart index position
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
    public function getChartNames(): array
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
        foreach ($this->chartCollection as $index => $chart) {
            if ($chart->getName() == $chartName) {
                return $chart;
            }
        }

        return false;
    }

    /**
     * Refresh column dimensions.
     *
     * @return $this
     */
    public function refreshColumnDimensions(): static
    {
        $newColumnDimensions = [];
        foreach ($this->getColumnDimensions() as $objColumnDimension) {
            $newColumnDimensions[$objColumnDimension->getColumnIndex()] = $objColumnDimension;
        }

        $this->columnDimensions = $newColumnDimensions;

        return $this;
    }

    /**
     * Refresh row dimensions.
     *
     * @return $this
     */
    public function refreshRowDimensions(): static
    {
        $newRowDimensions = [];
        foreach ($this->getRowDimensions() as $objRowDimension) {
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
    public function calculateWorksheetDimension(): string
    {
        // Return
        return 'A1:' . $this->getHighestColumn() . $this->getHighestRow();
    }

    /**
     * Calculate worksheet data dimension.
     *
     * @return string String containing the dimension of this worksheet that actually contain data
     */
    public function calculateWorksheetDataDimension(): string
    {
        // Return
        return 'A1:' . $this->getHighestDataColumn() . $this->getHighestDataRow();
    }

    /**
     * Calculate widths for auto-size columns.
     *
     * @return $this
     */
    public function calculateColumnWidths(): static
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

            $autoFilterIndentRanges = (new AutoFit($this))->getAutoFilterIndentRanges();

            // loop through all cells in the worksheet
            foreach ($this->getCoordinates(false) as $coordinate) {
                $cell = $this->getCellOrNull($coordinate);

                if ($cell !== null && isset($autoSizes[$this->getCellCollection()->getCurrentColumn()])) {
                    //Determine if cell is in merge range
                    $isMerged = isset($isMergeCell[$this->getCellCollection()->getCurrentCoordinate()]);

                    //By default merged cells should be ignored
                    $isMergedButProceed = false;

                    //The only exception is if it's a merge range value cell of a 'vertical' range (1 column wide)
                    if ($isMerged && $cell->isMergeRangeValueCell()) {
                        $range = (string) $cell->getMergeRange();
                        $rangeBoundaries = Coordinate::rangeDimension($range);
                        if ($rangeBoundaries[0] === 1) {
                            $isMergedButProceed = true;
                        }
                    }

                    // Determine width if cell is not part of a merge or does and is a value cell of 1-column wide range
                    if (!$isMerged || $isMergedButProceed) {
                        // Determine if we need to make an adjustment for the first row in an AutoFilter range that
                        //    has a column filter dropdown
                        $filterAdjustment = false;
                        if (!empty($autoFilterIndentRanges)) {
                            foreach ($autoFilterIndentRanges as $autoFilterFirstRowRange) {
                                if ($cell->isInRange($autoFilterFirstRowRange)) {
                                    $filterAdjustment = true;

                                    break;
                                }
                            }
                        }

                        $indentAdjustment = $cell->getStyle()->getAlignment()->getIndent();
                        $indentAdjustment += (int) ($cell->getStyle()->getAlignment()->getHorizontal() === Alignment::HORIZONTAL_CENTER);

                        // Calculated value
                        // To formatted string
                        $cellValue = NumberFormat::toFormattedString(
                            $cell->getCalculatedValue(),
                            (string) $this->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex())
                                ->getNumberFormat()->getFormatCode()
                        );

                        if ($cellValue !== null && $cellValue !== '') {
                            $autoSizes[$this->getCellCollection()->getCurrentColumn()] = max(
                                $autoSizes[$this->getCellCollection()->getCurrentColumn()],
                                round(
                                    Shared\Font::calculateColumnWidth(
                                        $this->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex())->getFont(),
                                        $cellValue,
                                        (int) $this->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex())
                                            ->getAlignment()->getTextRotation(),
                                        $this->getParentOrThrow()->getDefaultStyle()->getFont(),
                                        $filterAdjustment,
                                        $indentAdjustment
                                    ),
                                    3
                                )
                            );
                        }
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
     * Get parent or null.
     */
    public function getParent(): ?Spreadsheet
    {
        return $this->parent;
    }

    /**
     * Get parent, throw exception if null.
     */
    public function getParentOrThrow(): Spreadsheet
    {
        if ($this->parent !== null) {
            return $this->parent;
        }

        throw new Exception('Sheet does not have a parent.');
    }

    /**
     * Re-bind parent.
     *
     * @return $this
     */
    public function rebindParent(Spreadsheet $parent): static
    {
        if ($this->parent !== null) {
            $definedNames = $this->parent->getDefinedNames();
            foreach ($definedNames as $definedName) {
                $parent->addDefinedName($definedName);
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
     * @param string $title String containing the dimension of this worksheet
     * @param bool $updateFormulaCellReferences Flag indicating whether cell references in formulae should
     *            be updated to reflect the new sheet name.
     *          This should be left as the default true, unless you are
     *          certain that no formula cells on any worksheet contain
     *          references to this worksheet
     * @param bool $validate False to skip validation of new title. WARNING: This should only be set
     *                       at parse time (by Readers), where titles can be assumed to be valid.
     *
     * @return $this
     */
    public function setTitle($title, $updateFormulaCellReferences = true, $validate = true): static
    {
        // Is this a 'rename' or not?
        if ($this->getTitle() == $title) {
            return $this;
        }

        // Old title
        $oldTitle = $this->getTitle();

        if ($validate) {
            // Syntax check
            self::checkSheetTitle($title);

            if ($this->parent) {
                // Is there already such sheet name?
                if ($this->parent->sheetNameExists($title)) {
                    // Use name, but append with lowest possible integer

                    if (Shared\StringHelper::countCharacters($title) > 29) {
                        $title = Shared\StringHelper::substring($title, 0, 29);
                    }
                    $i = 1;
                    while ($this->parent->sheetNameExists($title . ' ' . $i)) {
                        ++$i;
                        if ($i == 10) {
                            if (Shared\StringHelper::countCharacters($title) > 28) {
                                $title = Shared\StringHelper::substring($title, 0, 28);
                            }
                        } elseif ($i == 100) {
                            if (Shared\StringHelper::countCharacters($title) > 27) {
                                $title = Shared\StringHelper::substring($title, 0, 27);
                            }
                        }
                    }

                    $title .= " $i";
                }
            }
        }

        // Set title
        $this->title = $title;
        $this->dirty = true;

        if ($this->parent && $this->parent->getCalculationEngine()) {
            // New title
            $newTitle = $this->getTitle();
            $this->parent->getCalculationEngine()
                ->renameCalculationCacheForWorksheet($oldTitle, $newTitle);
            if ($updateFormulaCellReferences) {
                ReferenceHelper::getInstance()->updateNamedFormulae($this->parent, $oldTitle, $newTitle);
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
     * @return $this
     */
    public function setSheetState($value): static
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
     * @return $this
     */
    public function setPageSetup(PageSetup $pageSetup): static
    {
        $this->pageSetup = $pageSetup;

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
     * @return $this
     */
    public function setPageMargins(PageMargins $pageMargins): static
    {
        $this->pageMargins = $pageMargins;

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
     * @return $this
     */
    public function setHeaderFooter(HeaderFooter $headerFooter): static
    {
        $this->headerFooter = $headerFooter;

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
     * @return $this
     */
    public function setSheetView(SheetView $sheetView): static
    {
        $this->sheetView = $sheetView;

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
     * @return $this
     */
    public function setProtection(Protection $protection): static
    {
        $this->protection = $protection;
        $this->dirty = true;

        return $this;
    }

    /**
     * Get highest worksheet column.
     *
     * @param null|int|string $row Return the data highest column for the specified row,
     *                                     or the highest column of any row if no row number is passed
     *
     * @return string Highest column name
     */
    public function getHighestColumn($row = null)
    {
        if ($row === null) {
            return Coordinate::stringFromColumnIndex($this->cachedHighestColumn);
        }

        return $this->getHighestDataColumn($row);
    }

    /**
     * Get highest worksheet column that contains data.
     *
     * @param null|int|string $row Return the highest data column for the specified row,
     *                                     or the highest data column of any row if no row number is passed
     *
     * @return string Highest column name that contains data
     */
    public function getHighestDataColumn($row = null)
    {
        return $this->getCellCollection()->getHighestColumn($row);
    }

    /**
     * Get highest worksheet row.
     *
     * @param null|string $column Return the highest data row for the specified column,
     *                                     or the highest row of any column if no column letter is passed
     *
     * @return int Highest row number
     */
    public function getHighestRow($column = null)
    {
        if ($column === null) {
            return $this->cachedHighestRow;
        }

        return $this->getHighestDataRow($column);
    }

    /**
     * Get highest worksheet row that contains data.
     *
     * @param null|string $column Return the highest data row for the specified column,
     *                                     or the highest data row of any column if no column letter is passed
     *
     * @return int Highest row number that contains data
     */
    public function getHighestDataRow($column = null)
    {
        return $this->getCellCollection()->getHighestRow($column);
    }

    /**
     * Get highest worksheet column and highest row that have cell records.
     *
     * @return array Highest column name and highest row number
     */
    public function getHighestRowAndColumn(): array
    {
        return $this->getCellCollection()->getHighestRowAndColumn();
    }

    /**
     * Set a cell value.
     *
     * @param array<int>|CellAddress|string $coordinate Coordinate of the cell as a string, eg: 'C5';
     *               or as an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @param mixed $value Value for the cell
     * @param null|IValueBinder $binder Value Binder to override the currently set Value Binder
     *
     * @return $this
     */
    public function setCellValue($coordinate, mixed $value, ?IValueBinder $binder = null): static
    {
        $cellAddress = Functions::trimSheetFromCellReference(Validations::validateCellAddress($coordinate));
        $this->getCell($cellAddress)->setValue($value, $binder);

        return $this;
    }

    /**
     * Set a cell value by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the setCellValue() method with a cell address such as 'C5' instead;,
     *          or passing in an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @see Worksheet::setCellValue()
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     * @param mixed $value Value of the cell
     * @param null|IValueBinder $binder Value Binder to override the currently set Value Binder
     *
     * @return $this
     */
    public function setCellValueByColumnAndRow($columnIndex, $row, mixed $value, ?IValueBinder $binder = null): static
    {
        $this->getCell(Coordinate::stringFromColumnIndex($columnIndex) . $row)->setValue($value, $binder);

        return $this;
    }

    /**
     * Set a cell value.
     *
     * @param array<int>|CellAddress|string $coordinate Coordinate of the cell as a string, eg: 'C5';
     *               or as an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @param mixed $value Value of the cell
     * @param string $dataType Explicit data type, see DataType::TYPE_*
     *        Note that PhpSpreadsheet does not validate that the value and datatype are consistent, in using this
     *             method, then it is your responsibility as an end-user developer to validate that the value and
     *             the datatype match.
     *       If you do mismatch value and datatpe, then the value you enter may be changed to match the datatype
     *          that you specify.
     *
     * @see DataType
     *
     * @return $this
     */
    public function setCellValueExplicit($coordinate, mixed $value, string $dataType): static
    {
        $cellAddress = Functions::trimSheetFromCellReference(Validations::validateCellAddress($coordinate));
        $this->getCell($cellAddress)->setValueExplicit($value, $dataType);

        return $this;
    }

    /**
     * Set a cell value by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the setCellValueExplicit() method with a cell address such as 'C5' instead;,
     *          or passing in an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @see Worksheet::setCellValueExplicit()
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     * @param mixed $value Value of the cell
     * @param string $dataType Explicit data type, see DataType::TYPE_*
     *        Note that PhpSpreadsheet does not validate that the value and datatype are consistent, in using this
     *             method, then it is your responsibility as an end-user developer to validate that the value and
     *             the datatype match.
     *       If you do mismatch value and datatpe, then the value you enter may be changed to match the datatype
     *          that you specify.
     *
     * @see DataType
     *
     * @return $this
     */
    public function setCellValueExplicitByColumnAndRow($columnIndex, $row, mixed $value, string $dataType): static
    {
        $this->getCell(Coordinate::stringFromColumnIndex($columnIndex) . $row)->setValueExplicit($value, $dataType);

        return $this;
    }

    /**
     * Get cell at a specific coordinate.
     *
     * @param array<int>|CellAddress|string $coordinate Coordinate of the cell as a string, eg: 'C5';
     *               or as an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     *
     * @return Cell Cell that was found or created
     *              WARNING: Because the cell collection can be cached to reduce memory, it only allows one
     *              "active" cell at a time in memory. If you assign that cell to a variable, then select
     *              another cell using getCell() or any of its variants, the newly selected cell becomes
     *              the "active" cell, and any previous assignment becomes a disconnected reference because
     *              the active cell has changed.
     */
    public function getCell($coordinate): Cell
    {
        $cellAddress = Functions::trimSheetFromCellReference(Validations::validateCellAddress($coordinate));

        // Shortcut for increased performance for the vast majority of simple cases
        if ($this->getCellCollection()->has($cellAddress)) {
            /** @var Cell $cell */
            $cell = $this->getCellCollection()->get($cellAddress);

            return $cell;
        }

        /** @var Worksheet $sheet */
        [$sheet, $finalCoordinate] = $this->getWorksheetAndCoordinate($cellAddress);
        $cell = $sheet->getCellCollection()->get($finalCoordinate);

        return $cell ?? $sheet->createNewCell($finalCoordinate);
    }

    /**
     * Get the correct Worksheet and coordinate from a coordinate that may
     * contains reference to another sheet or a named range.
     *
     * @return array{0: Worksheet, 1: string}
     */
    private function getWorksheetAndCoordinate(string $coordinate): array
    {
        $sheet = null;
        $finalCoordinate = null;

        // Worksheet reference?
        if (str_contains($coordinate, '!')) {
            $worksheetReference = self::extractSheetTitle($coordinate, true);

            $sheet = $this->getParentOrThrow()->getSheetByName($worksheetReference[0]);
            $finalCoordinate = strtoupper($worksheetReference[1]);

            if ($sheet === null) {
                throw new Exception('Sheet not found for name: ' . $worksheetReference[0]);
            }
        } elseif (
            !preg_match('/^' . Calculation::CALCULATION_REGEXP_CELLREF . '$/i', $coordinate)
            && preg_match('/^' . Calculation::CALCULATION_REGEXP_DEFINEDNAME . '$/iu', $coordinate)
        ) {
            // Named range?
            $namedRange = $this->validateNamedRange($coordinate, true);
            if ($namedRange !== null) {
                $sheet = $namedRange->getWorksheet();
                if ($sheet === null) {
                    throw new Exception('Sheet not found for named range: ' . $namedRange->getName());
                }

                /** @phpstan-ignore-next-line */
                $cellCoordinate = ltrim(substr($namedRange->getValue(), strrpos($namedRange->getValue(), '!')), '!');
                $finalCoordinate = str_replace('$', '', $cellCoordinate);
            }
        }

        if ($sheet === null || $finalCoordinate === null) {
            $sheet = $this;
            $finalCoordinate = strtoupper($coordinate);
        }

        if (Coordinate::coordinateIsRange($finalCoordinate)) {
            throw new Exception('Cell coordinate string can not be a range of cells.');
        } elseif (str_contains($finalCoordinate, '$')) {
            throw new Exception('Cell coordinate must not be absolute.');
        }

        return [$sheet, $finalCoordinate];
    }

    /**
     * Get an existing cell at a specific coordinate, or null.
     *
     * @param string $coordinate Coordinate of the cell, eg: 'A1'
     *
     * @return null|Cell Cell that was found or null
     */
    private function getCellOrNull($coordinate): ?Cell
    {
        // Check cell collection
        if ($this->getCellCollection()->has($coordinate)) {
            return $this->getCellCollection()->get($coordinate);
        }

        return null;
    }

    /**
     * Get cell at a specific coordinate by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the getCell() method with a cell address such as 'C5' instead;,
     *          or passing in an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @see Worksheet::getCell()
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     *
     * @return Cell Cell that was found/created or null
     *              WARNING: Because the cell collection can be cached to reduce memory, it only allows one
     *              "active" cell at a time in memory. If you assign that cell to a variable, then select
     *              another cell using getCell() or any of its variants, the newly selected cell becomes
     *              the "active" cell, and any previous assignment becomes a disconnected reference because
     *              the active cell has changed.
     */
    public function getCellByColumnAndRow($columnIndex, $row): Cell
    {
        return $this->getCell(Coordinate::stringFromColumnIndex($columnIndex) . $row);
    }

    /**
     * Create a new cell at the specified coordinate.
     *
     * @param string $coordinate Coordinate of the cell
     *
     * @return Cell Cell that was created
     *              WARNING: Because the cell collection can be cached to reduce memory, it only allows one
     *              "active" cell at a time in memory. If you assign that cell to a variable, then select
     *              another cell using getCell() or any of its variants, the newly selected cell becomes
     *              the "active" cell, and any previous assignment becomes a disconnected reference because
     *              the active cell has changed.
     */
    public function createNewCell(string $coordinate): Cell
    {
        [$column, $row, $columnString] = Coordinate::indexesFromString($coordinate);
        $cell = new Cell(null, DataType::TYPE_NULL, $this);
        $this->getCellCollection()->add($coordinate, $cell);

        // Coordinates
        if ($column > $this->cachedHighestColumn) {
            $this->cachedHighestColumn = $column;
        }
        if ($row > $this->cachedHighestRow) {
            $this->cachedHighestRow = $row;
        }

        // Cell needs appropriate xfIndex from dimensions records
        //    but don't create dimension records if they don't already exist
        $rowDimension = $this->rowDimensions[$row] ?? null;
        $columnDimension = $this->columnDimensions[$columnString] ?? null;

        $xfSet = false;
        if ($rowDimension !== null) {
            $rowXf = (int) $rowDimension->getXfIndex();
            if ($rowXf > 0) {
                // then there is a row dimension with explicit style, assign it to the cell
                $cell->setXfIndex($rowXf);
                $xfSet = true;
            }
        }
        if (!$xfSet && $columnDimension !== null) {
            $colXf = (int) $columnDimension->getXfIndex();
            if ($colXf > 0) {
                // then there is a column dimension, assign it to the cell
                $cell->setXfIndex($colXf);
            }
        }

        return $cell;
    }

    /**
     * Does the cell at a specific coordinate exist?
     *
     * @param array<int>|CellAddress|string $coordinate Coordinate of the cell as a string, eg: 'C5';
     *               or as an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     */
    public function cellExists($coordinate): bool
    {
        $cellAddress = Validations::validateCellAddress($coordinate);
        [$sheet, $finalCoordinate] = $this->getWorksheetAndCoordinate($cellAddress);

        return $sheet->getCellCollection()->has($finalCoordinate);
    }

    /**
     * Cell at a specific coordinate by using numeric cell coordinates exists?
     *
     * @deprecated 1.23.0
     *      Use the cellExists() method with a cell address such as 'C5' instead;,
     *          or passing in an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @see Worksheet::cellExists()
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     */
    public function cellExistsByColumnAndRow($columnIndex, $row): bool
    {
        return $this->cellExists(Coordinate::stringFromColumnIndex($columnIndex) . $row);
    }

    /**
     * Get row dimension at a specific row.
     *
     * @param int $row Numeric index of the row
     */
    public function getRowDimension(int $row): RowDimension
    {
        // Get row dimension
        if (!isset($this->rowDimensions[$row])) {
            $this->rowDimensions[$row] = new RowDimension($row);

            $this->cachedHighestRow = max($this->cachedHighestRow, $row);
        }

        return $this->rowDimensions[$row];
    }

    public function rowDimensionExists(int $row): bool
    {
        return isset($this->rowDimensions[$row]);
    }

    public function columnDimensionExists(string $column): bool
    {
        return isset($this->columnDimensions[$column]);
    }

    /**
     * Get column dimension at a specific column.
     *
     * @param string $column String index of the column eg: 'A'
     */
    public function getColumnDimension(string $column): ColumnDimension
    {
        // Uppercase coordinate
        $column = strtoupper($column);

        // Fetch dimensions
        if (!isset($this->columnDimensions[$column])) {
            $this->columnDimensions[$column] = new ColumnDimension($column);

            $columnIndex = Coordinate::columnIndexFromString($column);
            if ($this->cachedHighestColumn < $columnIndex) {
                $this->cachedHighestColumn = $columnIndex;
            }
        }

        return $this->columnDimensions[$column];
    }

    /**
     * Get column dimension at a specific column by using numeric cell coordinates.
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     */
    public function getColumnDimensionByColumn(int $columnIndex): ColumnDimension
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
     * @param AddressRange|array{0: int, 1: int, 2: int, 3: int}|array{0: int, 1: int}|CellAddress|int|string $cellCoordinate
     *              A simple string containing a cell address like 'A1' or a cell range like 'A1:E10'
     *              or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *              or a CellAddress or AddressRange object.
     */
    public function getStyle($cellCoordinate): Style
    {
        $cellCoordinate = Validations::validateCellOrCellRange($cellCoordinate);

        // set this sheet as active
        $this->getParentOrThrow()->setActiveSheetIndex($this->getParentOrThrow()->getIndex($this));

        // set cell coordinate as active
        $this->setSelectedCells($cellCoordinate);

        return $this->getParentOrThrow()->getCellXfSupervisor();
    }

    /**
     * Get style for cell by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the getStyle() method with a cell address range such as 'C5:F8' instead;,
     *          or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *          or an AddressRange object.
     * @see Worksheet::getStyle()
     *
     * @param int $columnIndex1 Numeric column coordinate of the cell
     * @param int $row1 Numeric row coordinate of the cell
     * @param null|int $columnIndex2 Numeric column coordinate of the range cell
     * @param null|int $row2 Numeric row coordinate of the range cell
     */
    public function getStyleByColumnAndRow($columnIndex1, $row1, $columnIndex2 = null, $row2 = null): Style
    {
        if ($columnIndex2 !== null && $row2 !== null) {
            $cellRange = new CellRange(
                CellAddress::fromColumnAndRow($columnIndex1, $row1),
                CellAddress::fromColumnAndRow($columnIndex2, $row2)
            );

            return $this->getStyle($cellRange);
        }

        return $this->getStyle(CellAddress::fromColumnAndRow($columnIndex1, $row1));
    }

    /**
     * Get conditional styles for a cell.
     *
     * @param string $coordinate eg: 'A1' or 'A1:A3'.
     *          If a single cell is referenced, then the array of conditional styles will be returned if the cell is
     *               included in a conditional style range.
     *          If a range of cells is specified, then the styles will only be returned if the range matches the entire
     *               range of the conditional.
     *
     * @return Conditional[]
     */
    public function getConditionalStyles(string $coordinate): array
    {
        $coordinate = strtoupper($coordinate);
        if (str_contains($coordinate, ':')) {
            return $this->conditionalStylesCollection[$coordinate] ?? [];
        }

        $cell = $this->getCell($coordinate);
        foreach (array_keys($this->conditionalStylesCollection) as $conditionalRange) {
            if ($cell->isInRange($conditionalRange)) {
                return $this->conditionalStylesCollection[$conditionalRange];
            }
        }

        return [];
    }

    public function getConditionalRange(string $coordinate): ?string
    {
        $coordinate = strtoupper($coordinate);
        $cell = $this->getCell($coordinate);
        foreach (array_keys($this->conditionalStylesCollection) as $conditionalRange) {
            if ($cell->isInRange($conditionalRange)) {
                return $conditionalRange;
            }
        }

        return null;
    }

    /**
     * Do conditional styles exist for this cell?
     *
     * @param string $coordinate eg: 'A1' or 'A1:A3'.
     *          If a single cell is specified, then this method will return true if that cell is included in a
     *               conditional style range.
     *          If a range of cells is specified, then true will only be returned if the range matches the entire
     *               range of the conditional.
     */
    public function conditionalStylesExists($coordinate): bool
    {
        $coordinate = strtoupper($coordinate);
        if (str_contains($coordinate, ':')) {
            return isset($this->conditionalStylesCollection[$coordinate]);
        }

        $cell = $this->getCell($coordinate);
        foreach (array_keys($this->conditionalStylesCollection) as $conditionalRange) {
            if ($cell->isInRange($conditionalRange)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes conditional styles for a cell.
     *
     * @param string $coordinate eg: 'A1'
     *
     * @return $this
     */
    public function removeConditionalStyles($coordinate): static
    {
        unset($this->conditionalStylesCollection[strtoupper($coordinate)]);

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
     * @param string $coordinate eg: 'A1'
     * @param Conditional[] $styles
     *
     * @return $this
     */
    public function setConditionalStyles($coordinate, $styles): static
    {
        $this->conditionalStylesCollection[strtoupper($coordinate)] = $styles;

        return $this;
    }

    /**
     * Duplicate cell style to a range of cells.
     *
     * Please note that this will overwrite existing cell styles for cells in range!
     *
     * @param Style $style Cell style to duplicate
     * @param string $range Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     *
     * @return $this
     */
    public function duplicateStyle(Style $style, string $range): static
    {
        // Add the style to the workbook if necessary
        $workbook = $this->getParentOrThrow();
        if ($existingStyle = $workbook->getCellXfByHashCode($style->getHashCode())) {
            // there is already such cell Xf in our collection
            $xfIndex = $existingStyle->getIndex();
        } else {
            // we don't have such a cell Xf, need to add
            $workbook->addCellXf($style);
            $xfIndex = $style->getIndex();
        }

        // Calculate range outer borders
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($range . ':' . $range);

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
     * @param Conditional[] $styles Cell style to duplicate
     * @param string $range Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     *
     * @return $this
     */
    public function duplicateConditionalStyle(array $styles, string $range = ''): static
    {
        foreach ($styles as $cellStyle) {
            if (!($cellStyle instanceof Conditional)) {
                throw new Exception('Style is not a conditional style');
            }
        }

        // Calculate range outer borders
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($range . ':' . $range);

        // Make sure we can loop upwards on rows and columns
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd = $tmp;
        }

        // Loop through cells and apply styles
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->setConditionalStyles(Coordinate::stringFromColumnIndex($col) . $row, $styles);
            }
        }

        return $this;
    }

    /**
     * Set break on a cell.
     *
     * @param array<int>|CellAddress|string $coordinate Coordinate of the cell as a string, eg: 'C5';
     *               or as an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @param int $break Break type (type of Worksheet::BREAK_*)
     *
     * @return $this
     */
    public function setBreak($coordinate, $break, int $max = -1): static
    {
        $cellAddress = Functions::trimSheetFromCellReference(Validations::validateCellAddress($coordinate));

        if ($break === self::BREAK_NONE) {
            unset($this->rowBreaks[$cellAddress], $this->columnBreaks[$cellAddress]);
        } elseif ($break === self::BREAK_ROW) {
            $this->rowBreaks[$cellAddress] = new PageBreak($break, $cellAddress, $max);
        } elseif ($break === self::BREAK_COLUMN) {
            $this->columnBreaks[$cellAddress] = new PageBreak($break, $cellAddress, $max);
        }

        return $this;
    }

    /**
     * Set break on a cell by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the setBreak() method with a cell address such as 'C5' instead;,
     *          or passing in an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @see Worksheet::setBreak()
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     * @param int $break Break type (type of Worksheet::BREAK_*)
     *
     * @return $this
     */
    public function setBreakByColumnAndRow($columnIndex, $row, $break): static
    {
        return $this->setBreak(Coordinate::stringFromColumnIndex($columnIndex) . $row, $break);
    }

    /**
     * Get breaks.
     *
     * @return int[]
     */
    public function getBreaks(): array
    {
        $breaks = [];
        /** @var callable */
        $compareFunction = [self::class, 'compareRowBreaks'];
        uksort($this->rowBreaks, $compareFunction);
        foreach ($this->rowBreaks as $break) {
            $breaks[$break->getCoordinate()] = self::BREAK_ROW;
        }
        /** @var callable */
        $compareFunction = [self::class, 'compareColumnBreaks'];
        uksort($this->columnBreaks, $compareFunction);
        foreach ($this->columnBreaks as $break) {
            $breaks[$break->getCoordinate()] = self::BREAK_COLUMN;
        }

        return $breaks;
    }

    /**
     * Get row breaks.
     *
     * @return PageBreak[]
     */
    public function getRowBreaks()
    {
        /** @var callable */
        $compareFunction = [self::class, 'compareRowBreaks'];
        uksort($this->rowBreaks, $compareFunction);

        return $this->rowBreaks;
    }

    protected static function compareRowBreaks(string $coordinate1, string $coordinate2): int
    {
        $row1 = Coordinate::indexesFromString($coordinate1)[1];
        $row2 = Coordinate::indexesFromString($coordinate2)[1];

        return $row1 - $row2;
    }

    protected static function compareColumnBreaks(string $coordinate1, string $coordinate2): int
    {
        $column1 = Coordinate::indexesFromString($coordinate1)[0];
        $column2 = Coordinate::indexesFromString($coordinate2)[0];

        return $column1 - $column2;
    }

    /**
     * Get column breaks.
     *
     * @return PageBreak[]
     */
    public function getColumnBreaks()
    {
        /** @var callable */
        $compareFunction = [self::class, 'compareColumnBreaks'];
        uksort($this->columnBreaks, $compareFunction);

        return $this->columnBreaks;
    }

    /**
     * Set merge on a cell range.
     *
     * @param AddressRange|array{0: int, 1: int, 2: int, 3: int}|array{0: int, 1: int}|string $range A simple string containing a Cell range like 'A1:E10'
     *              or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *              or an AddressRange.
     * @param string $behaviour How the merged cells should behave.
     *               Possible values are:
     *                   MERGE_CELL_CONTENT_EMPTY - Empty the content of the hidden cells
     *                   MERGE_CELL_CONTENT_HIDE - Keep the content of the hidden cells
     *                   MERGE_CELL_CONTENT_MERGE - Move the content of the hidden cells into the first cell
     *
     * @return $this
     */
    public function mergeCells($range, $behaviour = self::MERGE_CELL_CONTENT_EMPTY): static
    {
        $range = Functions::trimSheetFromCellReference(Validations::validateCellRange($range));

        if (!str_contains($range, ':')) {
            $range .= ":{$range}";
        }

        if (preg_match('/^([A-Z]+)(\\d+):([A-Z]+)(\\d+)$/', $range, $matches) !== 1) {
            throw new Exception('Merge must be on a valid range of cells.');
        }

        $this->mergeCells[$range] = $range;
        $firstRow = (int) $matches[2];
        $lastRow = (int) $matches[4];
        $firstColumn = $matches[1];
        $lastColumn = $matches[3];
        $firstColumnIndex = Coordinate::columnIndexFromString($firstColumn);
        $lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);
        $numberRows = $lastRow - $firstRow;
        $numberColumns = $lastColumnIndex - $firstColumnIndex;

        if ($numberRows === 1 && $numberColumns === 1) {
            return $this;
        }

        // create upper left cell if it does not already exist
        $upperLeft = "{$firstColumn}{$firstRow}";
        if (!$this->cellExists($upperLeft)) {
            $this->getCell($upperLeft)->setValueExplicit(null, DataType::TYPE_NULL);
        }

        if ($behaviour !== self::MERGE_CELL_CONTENT_HIDE) {
            // Blank out the rest of the cells in the range (if they exist)
            if ($numberRows > $numberColumns) {
                $this->clearMergeCellsByColumn($firstColumn, $lastColumn, $firstRow, $lastRow, $upperLeft, $behaviour);
            } else {
                $this->clearMergeCellsByRow($firstColumn, $lastColumnIndex, $firstRow, $lastRow, $upperLeft, $behaviour);
            }
        }

        return $this;
    }

    private function clearMergeCellsByColumn(string $firstColumn, string $lastColumn, int $firstRow, int $lastRow, string $upperLeft, string $behaviour): void
    {
        $leftCellValue = ($behaviour === self::MERGE_CELL_CONTENT_MERGE)
            ? [$this->getCell($upperLeft)->getFormattedValue()]
            : [];

        foreach ($this->getColumnIterator($firstColumn, $lastColumn) as $column) {
            $iterator = $column->getCellIterator($firstRow);
            $iterator->setIterateOnlyExistingCells(true);
            foreach ($iterator as $cell) {
                if ($cell !== null) {
                    $row = $cell->getRow();
                    if ($row > $lastRow) {
                        break;
                    }
                    $leftCellValue = $this->mergeCellBehaviour($cell, $upperLeft, $behaviour, $leftCellValue);
                }
            }
        }

        if ($behaviour === self::MERGE_CELL_CONTENT_MERGE) {
            $this->getCell($upperLeft)->setValueExplicit(implode(' ', $leftCellValue), DataType::TYPE_STRING);
        }
    }

    private function clearMergeCellsByRow(string $firstColumn, int $lastColumnIndex, int $firstRow, int $lastRow, string $upperLeft, string $behaviour): void
    {
        $leftCellValue = ($behaviour === self::MERGE_CELL_CONTENT_MERGE)
            ? [$this->getCell($upperLeft)->getFormattedValue()]
            : [];

        foreach ($this->getRowIterator($firstRow, $lastRow) as $row) {
            $iterator = $row->getCellIterator($firstColumn);
            $iterator->setIterateOnlyExistingCells(true);
            foreach ($iterator as $cell) {
                if ($cell !== null) {
                    $column = $cell->getColumn();
                    $columnIndex = Coordinate::columnIndexFromString($column);
                    if ($columnIndex > $lastColumnIndex) {
                        break;
                    }
                    $leftCellValue = $this->mergeCellBehaviour($cell, $upperLeft, $behaviour, $leftCellValue);
                }
            }
        }

        if ($behaviour === self::MERGE_CELL_CONTENT_MERGE) {
            $this->getCell($upperLeft)->setValueExplicit(implode(' ', $leftCellValue), DataType::TYPE_STRING);
        }
    }

    public function mergeCellBehaviour(Cell $cell, string $upperLeft, string $behaviour, array $leftCellValue): array
    {
        if ($cell->getCoordinate() !== $upperLeft) {
            Calculation::getInstance($cell->getWorksheet()->getParentOrThrow())->flushInstance();
            if ($behaviour === self::MERGE_CELL_CONTENT_MERGE) {
                $cellValue = $cell->getFormattedValue();
                if ($cellValue !== '') {
                    $leftCellValue[] = $cellValue;
                }
            }
            $cell->setValueExplicit(null, DataType::TYPE_NULL);
        }

        return $leftCellValue;
    }

    /**
     * Set merge on a cell range by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the mergeCells() method with a cell address range such as 'C5:F8' instead;,
     *          or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *          or an AddressRange object.
     * @see Worksheet::mergeCells()
     *
     * @param int $columnIndex1 Numeric column coordinate of the first cell
     * @param int $row1 Numeric row coordinate of the first cell
     * @param int $columnIndex2 Numeric column coordinate of the last cell
     * @param int $row2 Numeric row coordinate of the last cell
     * @param string $behaviour How the merged cells should behave.
     *               Possible values are:
     *                   MERGE_CELL_CONTENT_EMPTY - Empty the content of the hidden cells
     *                   MERGE_CELL_CONTENT_HIDE - Keep the content of the hidden cells
     *                   MERGE_CELL_CONTENT_MERGE - Move the content of the hidden cells into the first cell
     *
     * @return $this
     */
    public function mergeCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2, $behaviour = self::MERGE_CELL_CONTENT_EMPTY): static
    {
        $cellRange = new CellRange(
            CellAddress::fromColumnAndRow($columnIndex1, $row1),
            CellAddress::fromColumnAndRow($columnIndex2, $row2)
        );

        return $this->mergeCells($cellRange, $behaviour);
    }

    /**
     * Remove merge on a cell range.
     *
     * @param AddressRange|array{0: int, 1: int, 2: int, 3: int}|array{0: int, 1: int}|string $range A simple string containing a Cell range like 'A1:E10'
     *              or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *              or an AddressRange.
     *
     * @return $this
     */
    public function unmergeCells($range): static
    {
        $range = Functions::trimSheetFromCellReference(Validations::validateCellRange($range));

        if (str_contains($range, ':')) {
            if (isset($this->mergeCells[$range])) {
                unset($this->mergeCells[$range]);
            } else {
                throw new Exception('Cell range ' . $range . ' not known as merged.');
            }
        } else {
            throw new Exception('Merge can only be removed from a range of cells.');
        }

        return $this;
    }

    /**
     * Remove merge on a cell range by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the unmergeCells() method with a cell address range such as 'C5:F8' instead;,
     *          or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *          or an AddressRange object.
     * @see Worksheet::unmergeCells()
     *
     * @param int $columnIndex1 Numeric column coordinate of the first cell
     * @param int $row1 Numeric row coordinate of the first cell
     * @param int $columnIndex2 Numeric column coordinate of the last cell
     * @param int $row2 Numeric row coordinate of the last cell
     *
     * @return $this
     */
    public function unmergeCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2): static
    {
        $cellRange = new CellRange(
            CellAddress::fromColumnAndRow($columnIndex1, $row1),
            CellAddress::fromColumnAndRow($columnIndex2, $row2)
        );

        return $this->unmergeCells($cellRange);
    }

    /**
     * Get merge cells array.
     *
     * @return string[]
     */
    public function getMergeCells()
    {
        return $this->mergeCells;
    }

    /**
     * Set merge cells array for the entire sheet. Use instead mergeCells() to merge
     * a single cell range.
     *
     * @param string[] $mergeCells
     *
     * @return $this
     */
    public function setMergeCells(array $mergeCells): static
    {
        $this->mergeCells = $mergeCells;

        return $this;
    }

    /**
     * Set protection on a cell or cell range.
     *
     * @param AddressRange|array{0: int, 1: int, 2: int, 3: int}|array{0: int, 1: int}|CellAddress|int|string $range A simple string containing a Cell range like 'A1:E10'
     *              or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *              or a CellAddress or AddressRange object.
     * @param string $password Password to unlock the protection
     * @param bool $alreadyHashed If the password has already been hashed, set this to true
     *
     * @return $this
     */
    public function protectCells($range, $password, $alreadyHashed = false): static
    {
        $range = Functions::trimSheetFromCellReference(Validations::validateCellOrCellRange($range));

        if (!$alreadyHashed) {
            $password = Shared\PasswordHasher::hashPassword($password);
        }
        $this->protectedCells[$range] = $password;

        return $this;
    }

    /**
     * Set protection on a cell range by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the protectCells() method with a cell address range such as 'C5:F8' instead;,
     *          or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *          or an AddressRange object.
     * @see Worksheet::protectCells()
     *
     * @param int $columnIndex1 Numeric column coordinate of the first cell
     * @param int $row1 Numeric row coordinate of the first cell
     * @param int $columnIndex2 Numeric column coordinate of the last cell
     * @param int $row2 Numeric row coordinate of the last cell
     * @param string $password Password to unlock the protection
     * @param bool $alreadyHashed If the password has already been hashed, set this to true
     *
     * @return $this
     */
    public function protectCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2, $password, $alreadyHashed = false): static
    {
        $cellRange = new CellRange(
            CellAddress::fromColumnAndRow($columnIndex1, $row1),
            CellAddress::fromColumnAndRow($columnIndex2, $row2)
        );

        return $this->protectCells($cellRange, $password, $alreadyHashed);
    }

    /**
     * Remove protection on a cell or cell range.
     *
     * @param AddressRange|array{0: int, 1: int, 2: int, 3: int}|array{0: int, 1: int}|CellAddress|int|string $range A simple string containing a Cell range like 'A1:E10'
     *              or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *              or a CellAddress or AddressRange object.
     *
     * @return $this
     */
    public function unprotectCells($range): static
    {
        $range = Functions::trimSheetFromCellReference(Validations::validateCellOrCellRange($range));

        if (isset($this->protectedCells[$range])) {
            unset($this->protectedCells[$range]);
        } else {
            throw new Exception('Cell range ' . $range . ' not known as protected.');
        }

        return $this;
    }

    /**
     * Remove protection on a cell range by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the unprotectCells() method with a cell address range such as 'C5:F8' instead;,
     *          or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *          or an AddressRange object.
     * @see Worksheet::unprotectCells()
     *
     * @param int $columnIndex1 Numeric column coordinate of the first cell
     * @param int $row1 Numeric row coordinate of the first cell
     * @param int $columnIndex2 Numeric column coordinate of the last cell
     * @param int $row2 Numeric row coordinate of the last cell
     *
     * @return $this
     */
    public function unprotectCellsByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2): static
    {
        $cellRange = new CellRange(
            CellAddress::fromColumnAndRow($columnIndex1, $row1),
            CellAddress::fromColumnAndRow($columnIndex2, $row2)
        );

        return $this->unprotectCells($cellRange);
    }

    /**
     * Get protected cells.
     *
     * @return string[]
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
     * @param AddressRange|array{0: int, 1: int, 2: int, 3: int}|array{0: int, 1: int}|AutoFilter|string $autoFilterOrRange
     *            A simple string containing a Cell range like 'A1:E10' is permitted for backward compatibility
     *              or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *              or an AddressRange.
     *
     * @return $this
     */
    public function setAutoFilter($autoFilterOrRange): static
    {
        if (is_object($autoFilterOrRange) && ($autoFilterOrRange instanceof AutoFilter)) {
            $this->autoFilter = $autoFilterOrRange;
        } else {
            $cellRange = Functions::trimSheetFromCellReference(Validations::validateCellRange($autoFilterOrRange));

            $this->autoFilter->setRange($cellRange);
        }

        return $this;
    }

    /**
     * Set Autofilter Range by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the setAutoFilter() method with a cell address range such as 'C5:F8' instead;,
     *          or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *          or an AddressRange object or AutoFilter object.
     * @see Worksheet::setAutoFilter()
     *
     * @param int $columnIndex1 Numeric column coordinate of the first cell
     * @param int $row1 Numeric row coordinate of the first cell
     * @param int $columnIndex2 Numeric column coordinate of the second cell
     * @param int $row2 Numeric row coordinate of the second cell
     *
     * @return $this
     */
    public function setAutoFilterByColumnAndRow($columnIndex1, $row1, $columnIndex2, $row2): static
    {
        $cellRange = new CellRange(
            CellAddress::fromColumnAndRow($columnIndex1, $row1),
            CellAddress::fromColumnAndRow($columnIndex2, $row2)
        );

        return $this->setAutoFilter($cellRange);
    }

    /**
     * Remove autofilter.
     */
    public function removeAutoFilter(): self
    {
        $this->autoFilter->setRange('');

        return $this;
    }

    /**
     * Get collection of Tables.
     *
     * @return ArrayObject<int, Table>
     */
    public function getTableCollection()
    {
        return $this->tableCollection;
    }

    /**
     * Add Table.
     *
     * @return $this
     */
    public function addTable(Table $table): self
    {
        $table->setWorksheet($this);
        $this->tableCollection[] = $table;

        return $this;
    }

    /**
     * @return string[] array of Table names
     */
    public function getTableNames(): array
    {
        $tableNames = [];

        foreach ($this->tableCollection as $table) {
            /** @var Table $table */
            $tableNames[] = $table->getName();
        }

        return $tableNames;
    }

    /**
     * @param string $name the table name to search
     *
     * @return null|Table The table from the tables collection, or null if not found
     */
    public function getTableByName(string $name): ?Table
    {
        $tableIndex = $this->getTableIndexByName($name);

        return ($tableIndex === null) ? null : $this->tableCollection[$tableIndex];
    }

    /**
     * @param string $name the table name to search
     *
     * @return null|int The index of the located table in the tables collection, or null if not found
     */
    protected function getTableIndexByName(string $name): ?int
    {
        $name = Shared\StringHelper::strToUpper($name);
        foreach ($this->tableCollection as $index => $table) {
            /** @var Table $table */
            if (Shared\StringHelper::strToUpper($table->getName()) === $name) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Remove Table by name.
     *
     * @param string $name Table name
     *
     * @return $this
     */
    public function removeTableByName(string $name): self
    {
        $tableIndex = $this->getTableIndexByName($name);

        if ($tableIndex !== null) {
            unset($this->tableCollection[$tableIndex]);
        }

        return $this;
    }

    /**
     * Remove collection of Tables.
     */
    public function removeTableCollection(): self
    {
        $this->tableCollection = new ArrayObject();

        return $this;
    }

    /**
     * Get Freeze Pane.
     *
     * @return null|string
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
     * @param null|array<int>|CellAddress|string $coordinate Coordinate of the cell as a string, eg: 'C5';
     *            or as an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     *        Passing a null value for this argument will clear any existing freeze pane for this worksheet.
     * @param null|array<int>|CellAddress|string $topLeftCell default position of the right bottom pane
     *            Coordinate of the cell as a string, eg: 'C5'; or as an array of [$columnIndex, $row] (e.g. [3, 5]),
     *            or a CellAddress object.
     *
     * @return $this
     */
    public function freezePane($coordinate, $topLeftCell = null, bool $frozenSplit = false): static
    {
        $this->panes = [
            'bottomRight' => null,
            'bottomLeft' => null,
            'topRight' => null,
            'topLeft' => null,
        ];
        $cellAddress = ($coordinate !== null)
            ? Functions::trimSheetFromCellReference(Validations::validateCellAddress($coordinate))
            : null;
        if ($cellAddress !== null && Coordinate::coordinateIsRange($cellAddress)) {
            throw new Exception('Freeze pane can not be set on a range of cells.');
        }
        $topLeftCell = ($topLeftCell !== null)
            ? Functions::trimSheetFromCellReference(Validations::validateCellAddress($topLeftCell))
            : null;

        if ($cellAddress !== null && $topLeftCell === null) {
            $coordinate = Coordinate::coordinateFromString($cellAddress);
            $topLeftCell = $coordinate[0] . $coordinate[1];
        }

        $topLeftCell = "$topLeftCell";
        $this->paneTopLeftCell = $topLeftCell;

        $this->freezePane = $cellAddress;
        $this->topLeftCell = $topLeftCell;
        if ($cellAddress === null) {
            $this->paneState = '';
            $this->xSplit = $this->ySplit = 0;
            $this->activePane = '';
        } else {
            $coordinates = Coordinate::indexesFromString($cellAddress);
            $this->xSplit = $coordinates[0] - 1;
            $this->ySplit = $coordinates[1] - 1;
            if ($this->xSplit > 0 || $this->ySplit > 0) {
                $this->paneState = $frozenSplit ? self::PANE_FROZENSPLIT : self::PANE_FROZEN;
                $this->setSelectedCellsActivePane();
            } else {
                $this->paneState = '';
                $this->freezePane = null;
                $this->activePane = '';
            }
        }

        return $this;
    }

    public function setTopLeftCell(string $topLeftCell): self
    {
        $this->topLeftCell = $topLeftCell;

        return $this;
    }

    /**
     * Freeze Pane by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the freezePane() method with a cell address such as 'C5' instead;,
     *          or passing in an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @see Worksheet::freezePane()
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     *
     * @return $this
     */
    public function freezePaneByColumnAndRow($columnIndex, $row): static
    {
        return $this->freezePane(Coordinate::stringFromColumnIndex($columnIndex) . $row);
    }

    /**
     * Unfreeze Pane.
     *
     * @return $this
     */
    public function unfreezePane(): static
    {
        return $this->freezePane(null);
    }

    /**
     * Get the default position of the right bottom pane.
     *
     * @return null|string
     */
    public function getTopLeftCell()
    {
        return $this->topLeftCell;
    }

    public function getPaneTopLeftCell(): string
    {
        return $this->paneTopLeftCell;
    }

    public function setPaneTopLeftCell(string $paneTopLeftCell): self
    {
        $this->paneTopLeftCell = $paneTopLeftCell;

        return $this;
    }

    public function usesPanes(): bool
    {
        return $this->xSplit > 0 || $this->ySplit > 0;
    }

    public function getPane(string $position): ?Pane
    {
        return $this->panes[$position] ?? null;
    }

    public function setPane(string $position, ?Pane $pane): self
    {
        if (array_key_exists($position, $this->panes)) {
            $this->panes[$position] = $pane;
        }

        return $this;
    }

    /** @return (null|Pane)[] */
    public function getPanes()
    {
        return $this->panes;
    }

    public function getActivePane(): string
    {
        return $this->activePane;
    }

    public function setActivePane(string $activePane): self
    {
        $this->activePane = array_key_exists($activePane, $this->panes) ? $activePane : '';

        return $this;
    }

    public function getXSplit(): int
    {
        return $this->xSplit;
    }

    public function setXSplit(int $xSplit): self
    {
        $this->xSplit = $xSplit;
        if (in_array($this->paneState, self::VALIDFROZENSTATE, true)) {
            $this->freezePane([$this->xSplit + 1, $this->ySplit + 1], $this->topLeftCell, $this->paneState === self::PANE_FROZENSPLIT);
        }

        return $this;
    }

    public function getYSplit(): int
    {
        return $this->ySplit;
    }

    public function setYSplit(int $ySplit): self
    {
        $this->ySplit = $ySplit;
        if (in_array($this->paneState, self::VALIDFROZENSTATE, true)) {
            $this->freezePane([$this->xSplit + 1, $this->ySplit + 1], $this->topLeftCell, $this->paneState === self::PANE_FROZENSPLIT);
        }

        return $this;
    }

    public function getPaneState(): string
    {
        return $this->paneState;
    }

    public const PANE_FROZEN = 'frozen';
    public const PANE_FROZENSPLIT = 'frozenSplit';
    public const PANE_SPLIT = 'split';
    private const VALIDPANESTATE = [self::PANE_FROZEN, self::PANE_SPLIT, self::PANE_FROZENSPLIT];
    private const VALIDFROZENSTATE = [self::PANE_FROZEN, self::PANE_FROZENSPLIT];

    public function setPaneState(string $paneState): self
    {
        $this->paneState = in_array($paneState, self::VALIDPANESTATE, true) ? $paneState : '';
        if (in_array($this->paneState, self::VALIDFROZENSTATE, true)) {
            $this->freezePane([$this->xSplit + 1, $this->ySplit + 1], $this->topLeftCell, $this->paneState === self::PANE_FROZENSPLIT);
        } else {
            $this->freezePane = null;
        }

        return $this;
    }

    /**
     * Insert a new row, updating all possible related data.
     *
     * @param int $before Insert before this row number
     * @param int $numberOfRows Number of new rows to insert
     *
     * @return $this
     */
    public function insertNewRowBefore(int $before, int $numberOfRows = 1): static
    {
        if ($before >= 1) {
            $objReferenceHelper = ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore('A' . $before, 0, $numberOfRows, $this);
        } else {
            throw new Exception('Rows can only be inserted before at least row 1.');
        }

        return $this;
    }

    /**
     * Insert a new column, updating all possible related data.
     *
     * @param string $before Insert before this column Name, eg: 'A'
     * @param int $numberOfColumns Number of new columns to insert
     *
     * @return $this
     */
    public function insertNewColumnBefore(string $before, int $numberOfColumns = 1): static
    {
        if (!is_numeric($before)) {
            $objReferenceHelper = ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore($before . '1', $numberOfColumns, 0, $this);
        } else {
            throw new Exception('Column references should not be numeric.');
        }

        return $this;
    }

    /**
     * Insert a new column, updating all possible related data.
     *
     * @param int $beforeColumnIndex Insert before this column ID (numeric column coordinate of the cell)
     * @param int $numberOfColumns Number of new columns to insert
     *
     * @return $this
     */
    public function insertNewColumnBeforeByIndex(int $beforeColumnIndex, int $numberOfColumns = 1): static
    {
        if ($beforeColumnIndex >= 1) {
            return $this->insertNewColumnBefore(Coordinate::stringFromColumnIndex($beforeColumnIndex), $numberOfColumns);
        }

        throw new Exception('Columns can only be inserted before at least column A (1).');
    }

    /**
     * Delete a row, updating all possible related data.
     *
     * @param int $row Remove rows, starting with this row number
     * @param int $numberOfRows Number of rows to remove
     *
     * @return $this
     */
    public function removeRow(int $row, int $numberOfRows = 1): static
    {
        if ($row < 1) {
            throw new Exception('Rows to be deleted should at least start from row 1.');
        }

        $holdRowDimensions = $this->removeRowDimensions($row, $numberOfRows);
        $highestRow = $this->getHighestDataRow();
        $removedRowsCounter = 0;

        for ($r = 0; $r < $numberOfRows; ++$r) {
            if ($row + $r <= $highestRow) {
                $this->getCellCollection()->removeRow($row + $r);
                ++$removedRowsCounter;
            }
        }

        $objReferenceHelper = ReferenceHelper::getInstance();
        $objReferenceHelper->insertNewBefore('A' . ($row + $numberOfRows), 0, -$numberOfRows, $this);
        for ($r = 0; $r < $removedRowsCounter; ++$r) {
            $this->getCellCollection()->removeRow($highestRow);
            --$highestRow;
        }

        $this->rowDimensions = $holdRowDimensions;

        return $this;
    }

    private function removeRowDimensions(int $row, int $numberOfRows): array
    {
        $highRow = $row + $numberOfRows - 1;
        $holdRowDimensions = [];
        foreach ($this->rowDimensions as $rowDimension) {
            $num = $rowDimension->getRowIndex();
            if ($num < $row) {
                $holdRowDimensions[$num] = $rowDimension;
            } elseif ($num > $highRow) {
                $num -= $numberOfRows;
                $cloneDimension = clone $rowDimension;
                $cloneDimension->setRowIndex($num);
                $holdRowDimensions[$num] = $cloneDimension;
            }
        }

        return $holdRowDimensions;
    }

    /**
     * Remove a column, updating all possible related data.
     *
     * @param string $column Remove columns starting with this column name, eg: 'A'
     * @param int $numberOfColumns Number of columns to remove
     *
     * @return $this
     */
    public function removeColumn(string $column, int $numberOfColumns = 1): static
    {
        if (is_numeric($column)) {
            throw new Exception('Column references should not be numeric.');
        }

        $highestColumn = $this->getHighestDataColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        $pColumnIndex = Coordinate::columnIndexFromString($column);

        $holdColumnDimensions = $this->removeColumnDimensions($pColumnIndex, $numberOfColumns);

        $column = Coordinate::stringFromColumnIndex($pColumnIndex + $numberOfColumns);
        $objReferenceHelper = ReferenceHelper::getInstance();
        $objReferenceHelper->insertNewBefore($column . '1', -$numberOfColumns, 0, $this);

        $this->columnDimensions = $holdColumnDimensions;

        if ($pColumnIndex > $highestColumnIndex) {
            return $this;
        }

        $maxPossibleColumnsToBeRemoved = $highestColumnIndex - $pColumnIndex + 1;

        for ($c = 0, $n = min($maxPossibleColumnsToBeRemoved, $numberOfColumns); $c < $n; ++$c) {
            $this->getCellCollection()->removeColumn($highestColumn);
            $highestColumn = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($highestColumn) - 1);
        }

        $this->garbageCollect();

        return $this;
    }

    private function removeColumnDimensions(int $pColumnIndex, int $numberOfColumns): array
    {
        $highCol = $pColumnIndex + $numberOfColumns - 1;
        $holdColumnDimensions = [];
        foreach ($this->columnDimensions as $columnDimension) {
            $num = $columnDimension->getColumnNumeric();
            if ($num < $pColumnIndex) {
                $str = $columnDimension->getColumnIndex();
                $holdColumnDimensions[$str] = $columnDimension;
            } elseif ($num > $highCol) {
                $cloneDimension = clone $columnDimension;
                $cloneDimension->setColumnNumeric($num - $numberOfColumns);
                $str = $cloneDimension->getColumnIndex();
                $holdColumnDimensions[$str] = $cloneDimension;
            }
        }

        return $holdColumnDimensions;
    }

    /**
     * Remove a column, updating all possible related data.
     *
     * @param int $columnIndex Remove starting with this column Index (numeric column coordinate)
     * @param int $numColumns Number of columns to remove
     *
     * @return $this
     */
    public function removeColumnByIndex(int $columnIndex, int $numColumns = 1): static
    {
        if ($columnIndex >= 1) {
            return $this->removeColumn(Coordinate::stringFromColumnIndex($columnIndex), $numColumns);
        }

        throw new Exception('Columns to be deleted should at least start from column A (1)');
    }

    /**
     * Show gridlines?
     */
    public function getShowGridlines(): bool
    {
        return $this->showGridlines;
    }

    /**
     * Set show gridlines.
     *
     * @param bool $showGridLines Show gridlines (true/false)
     *
     * @return $this
     */
    public function setShowGridlines(bool $showGridLines): self
    {
        $this->showGridlines = $showGridLines;

        return $this;
    }

    /**
     * Print gridlines?
     */
    public function getPrintGridlines(): bool
    {
        return $this->printGridlines;
    }

    /**
     * Set print gridlines.
     *
     * @param bool $printGridLines Print gridlines (true/false)
     *
     * @return $this
     */
    public function setPrintGridlines(bool $printGridLines): self
    {
        $this->printGridlines = $printGridLines;

        return $this;
    }

    /**
     * Show row and column headers?
     */
    public function getShowRowColHeaders(): bool
    {
        return $this->showRowColHeaders;
    }

    /**
     * Set show row and column headers.
     *
     * @param bool $showRowColHeaders Show row and column headers (true/false)
     *
     * @return $this
     */
    public function setShowRowColHeaders(bool $showRowColHeaders): self
    {
        $this->showRowColHeaders = $showRowColHeaders;

        return $this;
    }

    /**
     * Show summary below? (Row/Column outlining).
     */
    public function getShowSummaryBelow(): bool
    {
        return $this->showSummaryBelow;
    }

    /**
     * Set show summary below.
     *
     * @param bool $showSummaryBelow Show summary below (true/false)
     *
     * @return $this
     */
    public function setShowSummaryBelow(bool $showSummaryBelow): self
    {
        $this->showSummaryBelow = $showSummaryBelow;

        return $this;
    }

    /**
     * Show summary right? (Row/Column outlining).
     */
    public function getShowSummaryRight(): bool
    {
        return $this->showSummaryRight;
    }

    /**
     * Set show summary right.
     *
     * @param bool $showSummaryRight Show summary right (true/false)
     *
     * @return $this
     */
    public function setShowSummaryRight(bool $showSummaryRight): self
    {
        $this->showSummaryRight = $showSummaryRight;

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
     * @param Comment[] $comments
     *
     * @return $this
     */
    public function setComments(array $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Remove comment from cell.
     *
     * @param array<int>|CellAddress|string $cellCoordinate Coordinate of the cell as a string, eg: 'C5';
     *               or as an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     *
     * @return $this
     */
    public function removeComment($cellCoordinate): self
    {
        $cellAddress = Functions::trimSheetFromCellReference(Validations::validateCellAddress($cellCoordinate));

        if (Coordinate::coordinateIsRange($cellAddress)) {
            throw new Exception('Cell coordinate string can not be a range of cells.');
        } elseif (str_contains($cellAddress, '$')) {
            throw new Exception('Cell coordinate string must not be absolute.');
        } elseif ($cellAddress == '') {
            throw new Exception('Cell coordinate can not be zero-length string.');
        }
        // Check if we have a comment for this cell and delete it
        if (isset($this->comments[$cellAddress])) {
            unset($this->comments[$cellAddress]);
        }

        return $this;
    }

    /**
     * Get comment for cell.
     *
     * @param array<int>|CellAddress|string $cellCoordinate Coordinate of the cell as a string, eg: 'C5';
     *               or as an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     */
    public function getComment($cellCoordinate): Comment
    {
        $cellAddress = Functions::trimSheetFromCellReference(Validations::validateCellAddress($cellCoordinate));

        if (Coordinate::coordinateIsRange($cellAddress)) {
            throw new Exception('Cell coordinate string can not be a range of cells.');
        } elseif (str_contains($cellAddress, '$')) {
            throw new Exception('Cell coordinate string must not be absolute.');
        } elseif ($cellAddress == '') {
            throw new Exception('Cell coordinate can not be zero-length string.');
        }

        // Check if we already have a comment for this cell.
        if (isset($this->comments[$cellAddress])) {
            return $this->comments[$cellAddress];
        }

        // If not, create a new comment.
        $newComment = new Comment();
        $this->comments[$cellAddress] = $newComment;

        return $newComment;
    }

    /**
     * Get comment for cell by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the getComment() method with a cell address such as 'C5' instead;,
     *          or passing in an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @see Worksheet::getComment()
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     */
    public function getCommentByColumnAndRow($columnIndex, $row): Comment
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
     * @param string $coordinate Cell (i.e. A1)
     *
     * @return $this
     */
    public function setSelectedCell($coordinate): static
    {
        return $this->setSelectedCells($coordinate);
    }

    /**
     * Select a range of cells.
     *
     * @param AddressRange|array{0: int, 1: int, 2: int, 3: int}|array{0: int, 1: int}|CellAddress|int|string $coordinate A simple string containing a Cell range like 'A1:E10'
     *              or passing in an array of [$fromColumnIndex, $fromRow, $toColumnIndex, $toRow] (e.g. [3, 5, 6, 8]),
     *              or a CellAddress or AddressRange object.
     *
     * @return $this
     */
    public function setSelectedCells($coordinate): static
    {
        if (is_string($coordinate)) {
            $coordinate = Validations::definedNameToCoordinate($coordinate, $this);
        }
        $coordinate = Validations::validateCellOrCellRange($coordinate);

        if (Coordinate::coordinateIsRange($coordinate)) {
            [$first] = Coordinate::splitRange($coordinate);
            $this->activeCell = $first[0];
        } else {
            $this->activeCell = $coordinate;
        }
        $this->selectedCells = $coordinate;
        $this->setSelectedCellsActivePane();

        return $this;
    }

    private function setSelectedCellsActivePane(): void
    {
        if (!empty($this->freezePane)) {
            $coordinateC = Coordinate::indexesFromString($this->freezePane);
            $coordinateT = Coordinate::indexesFromString($this->activeCell);
            if ($coordinateC[0] === 1) {
                $activePane = ($coordinateT[1] <= $coordinateC[1]) ? 'topLeft' : 'bottomLeft';
            } elseif ($coordinateC[1] === 1) {
                $activePane = ($coordinateT[0] <= $coordinateC[0]) ? 'topLeft' : 'topRight';
            } elseif ($coordinateT[1] <= $coordinateC[1]) {
                $activePane = ($coordinateT[0] <= $coordinateC[0]) ? 'topLeft' : 'topRight';
            } else {
                $activePane = ($coordinateT[0] <= $coordinateC[0]) ? 'bottomLeft' : 'bottomRight';
            }
            $this->setActivePane($activePane);
            $this->panes[$activePane] = new Pane($activePane, $this->selectedCells, $this->activeCell);
        }
    }

    /**
     * Selected cell by using numeric cell coordinates.
     *
     * @deprecated 1.23.0
     *      Use the setSelectedCells() method with a cell address such as 'C5' instead;,
     *          or passing in an array of [$columnIndex, $row] (e.g. [3, 5]), or a CellAddress object.
     * @see Worksheet::setSelectedCells()
     *
     * @param int $columnIndex Numeric column coordinate of the cell
     * @param int $row Numeric row coordinate of the cell
     *
     * @return $this
     */
    public function setSelectedCellByColumnAndRow($columnIndex, $row): static
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
     * @return $this
     */
    public function setRightToLeft($value): static
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
     * @return $this
     */
    public function fromArray(array $source, mixed $nullValue = null, $startCell = 'A1', $strictNullComparison = false): static
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
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Calculation\Exception
     *
     * @return mixed
     */
    protected function cellToArray(Cell $cell, bool $calculateFormulas, bool $formatData, mixed $nullValue)
    {
        $returnValue = $nullValue;

        if ($cell->getValue() !== null) {
            if ($cell->getValue() instanceof RichText) {
                $returnValue = $cell->getValue()->getPlainText();
            } else {
                $returnValue = ($calculateFormulas) ? $cell->getCalculatedValue() : $cell->getValue();
            }

            if ($formatData) {
                $style = $this->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex());
                $returnValue = NumberFormat::toFormattedString(
                    $returnValue,
                    $style->getNumberFormat()->getFormatCode() ?? NumberFormat::FORMAT_GENERAL
                );
            }
        }

        return $returnValue;
    }

    /**
     * Create array from a range of cells.
     *
     * @param mixed $nullValue Value returned in the array entry if a cell doesn't exist
     * @param bool $calculateFormulas Should formulas be calculated?
     * @param bool $formatData Should formatting be applied to cell values?
     * @param bool $returnCellRef False - Return a simple array of rows and columns indexed by number counting from zero
     *                             True - Return rows and columns indexed by their actual row and column IDs
     * @param bool $ignoreHidden False - Return values for rows/columns even if they are defined as hidden.
     *                            True - Don't return values for rows/columns that are defined as hidden.
     */
    public function rangeToArray(
        string $range,
        mixed $nullValue = null,
        bool $calculateFormulas = true,
        bool $formatData = true,
        bool $returnCellRef = false,
        bool $ignoreHidden = false
    ): array {
        $range = Validations::validateCellOrCellRange($range);

        $returnValue = [];
        //    Identify the range that we need to extract from the worksheet
        [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($range);
        $minCol = Coordinate::stringFromColumnIndex($rangeStart[0]);
        $minRow = $rangeStart[1];
        $maxCol = Coordinate::stringFromColumnIndex($rangeEnd[0]);
        $maxRow = $rangeEnd[1];

        ++$maxCol;
        // Loop through rows
        $r = -1;
        for ($row = $minRow; $row <= $maxRow; ++$row) {
            if (($ignoreHidden === true) && ($this->getRowDimension($row)->getVisible() === false)) {
                continue;
            }
            $rowRef = $returnCellRef ? $row : ++$r;
            $c = -1;
            // Loop through columns in the current row
            for ($col = $minCol; $col !== $maxCol; ++$col) {
                if (($ignoreHidden === true) && ($this->getColumnDimension($col)->getVisible() === false)) {
                    continue;
                }
                $columnRef = $returnCellRef ? $col : ++$c;
                //    Using getCell() will create a new cell if it doesn't already exist. We don't want that to happen
                //        so we test and retrieve directly against cellCollection
                $cell = $this->getCellCollection()->get("{$col}{$row}");
                $returnValue[$rowRef][$columnRef] = $nullValue;
                if ($cell !== null) {
                    $returnValue[$rowRef][$columnRef] = $this->cellToArray($cell, $calculateFormulas, $formatData, $nullValue);
                }
            }
        }

        // Return
        return $returnValue;
    }

    private function validateNamedRange(string $definedName, bool $returnNullIfInvalid = false): ?DefinedName
    {
        $namedRange = DefinedName::resolveName($definedName, $this);
        if ($namedRange === null) {
            if ($returnNullIfInvalid) {
                return null;
            }

            throw new Exception('Named Range ' . $definedName . ' does not exist.');
        }

        if ($namedRange->isFormula()) {
            if ($returnNullIfInvalid) {
                return null;
            }

            throw new Exception('Defined Named ' . $definedName . ' is a formula, not a range or cell.');
        }

        if ($namedRange->getLocalOnly()) {
            $worksheet = $namedRange->getWorksheet();
            if ($worksheet === null || $this->getHashCode() !== $worksheet->getHashCode()) {
                if ($returnNullIfInvalid) {
                    return null;
                }

                throw new Exception(
                    'Named range ' . $definedName . ' is not accessible from within sheet ' . $this->getTitle()
                );
            }
        }

        return $namedRange;
    }

    /**
     * Create array from a range of cells.
     *
     * @param string $definedName The Named Range that should be returned
     * @param mixed $nullValue Value returned in the array entry if a cell doesn't exist
     * @param bool $calculateFormulas Should formulas be calculated?
     * @param bool $formatData Should formatting be applied to cell values?
     * @param bool $returnCellRef False - Return a simple array of rows and columns indexed by number counting from zero
     *                             True - Return rows and columns indexed by their actual row and column IDs
     * @param bool $ignoreHidden False - Return values for rows/columns even if they are defined as hidden.
     *                            True - Don't return values for rows/columns that are defined as hidden.
     */
    public function namedRangeToArray(
        string $definedName,
        mixed $nullValue = null,
        bool $calculateFormulas = true,
        bool $formatData = true,
        bool $returnCellRef = false,
        bool $ignoreHidden = false
    ): array {
        $retVal = [];
        $namedRange = $this->validateNamedRange($definedName);
        if ($namedRange !== null) {
            $cellRange = ltrim(substr($namedRange->getValue(), (int) strrpos($namedRange->getValue(), '!')), '!');
            $cellRange = str_replace('$', '', $cellRange);
            $workSheet = $namedRange->getWorksheet();
            if ($workSheet !== null) {
                $retVal = $workSheet->rangeToArray($cellRange, $nullValue, $calculateFormulas, $formatData, $returnCellRef, $ignoreHidden);
            }
        }

        return $retVal;
    }

    /**
     * Create array from worksheet.
     *
     * @param mixed $nullValue Value returned in the array entry if a cell doesn't exist
     * @param bool $calculateFormulas Should formulas be calculated?
     * @param bool $formatData Should formatting be applied to cell values?
     * @param bool $returnCellRef False - Return a simple array of rows and columns indexed by number counting from zero
     *                             True - Return rows and columns indexed by their actual row and column IDs
     * @param bool $ignoreHidden False - Return values for rows/columns even if they are defined as hidden.
     *                            True - Don't return values for rows/columns that are defined as hidden.
     */
    public function toArray(
        mixed $nullValue = null,
        bool $calculateFormulas = true,
        bool $formatData = true,
        bool $returnCellRef = false,
        bool $ignoreHidden = false
    ): array {
        // Garbage collect...
        $this->garbageCollect();

        //    Identify the range that we need to extract from the worksheet
        $maxCol = $this->getHighestColumn();
        $maxRow = $this->getHighestRow();

        // Return
        return $this->rangeToArray("A1:{$maxCol}{$maxRow}", $nullValue, $calculateFormulas, $formatData, $returnCellRef, $ignoreHidden);
    }

    /**
     * Get row iterator.
     *
     * @param int $startRow The row number at which to start iterating
     * @param int $endRow The row number at which to stop iterating
     */
    public function getRowIterator($startRow = 1, $endRow = null): RowIterator
    {
        return new RowIterator($this, $startRow, $endRow);
    }

    /**
     * Get column iterator.
     *
     * @param string $startColumn The column address at which to start iterating
     * @param string $endColumn The column address at which to stop iterating
     */
    public function getColumnIterator($startColumn = 'A', $endColumn = null): ColumnIterator
    {
        return new ColumnIterator($this, $startColumn, $endColumn);
    }

    /**
     * Run PhpSpreadsheet garbage collector.
     *
     * @return $this
     */
    public function garbageCollect(): static
    {
        // Flush cache
        $this->getCellCollection()->get('A1');

        // Lookup highest column and highest row if cells are cleaned
        $colRow = $this->getCellCollection()->getHighestRowAndColumn();
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
            $this->cachedHighestColumn = 1;
        } else {
            $this->cachedHighestColumn = $highestColumn;
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
     * Example: extractSheetTitle("testSheet!A1:C3") ==> 'A1:C3'
     * Example: extractSheetTitle("'testSheet 1'!A1", true) ==> ['testSheet 1', 'A1'];
     * Example: extractSheetTitle("'testSheet 1'!A1:C3", true) ==> ['testSheet 1', 'A1:C3'];
     * Example: extractSheetTitle("A1", true) ==> ['', 'A1'];
     * Example: extractSheetTitle("A1:C3", true) ==> ['', 'A1:C3']
     *
     * @param string $range Range to extract title from
     * @param bool $returnRange Return range? (see example)
     *
     * @return ($range is non-empty-string ? ($returnRange is true ? array{0: string, 1: string} : string) : ($returnRange is true ? array{0: null, 1: null} : null))
     */
    public static function extractSheetTitle($range, $returnRange = false): array|null|string
    {
        if (empty($range)) {
            return $returnRange ? [null, null] : null;
        }

        // Sheet title included?
        if (($sep = strrpos($range, '!')) === false) {
            return $returnRange ? ['', $range] : '';
        }

        if ($returnRange) {
            return [substr($range, 0, $sep), substr($range, $sep + 1)];
        }

        return substr($range, $sep + 1);
    }

    /**
     * Get hyperlink.
     *
     * @param string $cellCoordinate Cell coordinate to get hyperlink for, eg: 'A1'
     *
     * @return Hyperlink
     */
    public function getHyperlink($cellCoordinate)
    {
        // return hyperlink if we already have one
        if (isset($this->hyperlinkCollection[$cellCoordinate])) {
            return $this->hyperlinkCollection[$cellCoordinate];
        }

        // else create hyperlink
        $this->hyperlinkCollection[$cellCoordinate] = new Hyperlink();

        return $this->hyperlinkCollection[$cellCoordinate];
    }

    /**
     * Set hyperlink.
     *
     * @param string $cellCoordinate Cell coordinate to insert hyperlink, eg: 'A1'
     *
     * @return $this
     */
    public function setHyperlink($cellCoordinate, ?Hyperlink $hyperlink = null): static
    {
        if ($hyperlink === null) {
            unset($this->hyperlinkCollection[$cellCoordinate]);
        } else {
            $this->hyperlinkCollection[$cellCoordinate] = $hyperlink;
        }

        return $this;
    }

    /**
     * Hyperlink at a specific coordinate exists?
     *
     * @param string $coordinate eg: 'A1'
     */
    public function hyperlinkExists($coordinate): bool
    {
        return isset($this->hyperlinkCollection[$coordinate]);
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
     * @param string $cellCoordinate Cell coordinate to get data validation for, eg: 'A1'
     *
     * @return DataValidation
     */
    public function getDataValidation($cellCoordinate)
    {
        // return data validation if we already have one
        if (isset($this->dataValidationCollection[$cellCoordinate])) {
            return $this->dataValidationCollection[$cellCoordinate];
        }

        // else create data validation
        $this->dataValidationCollection[$cellCoordinate] = new DataValidation();

        return $this->dataValidationCollection[$cellCoordinate];
    }

    /**
     * Set data validation.
     *
     * @param string $cellCoordinate Cell coordinate to insert data validation, eg: 'A1'
     *
     * @return $this
     */
    public function setDataValidation($cellCoordinate, ?DataValidation $dataValidation = null): static
    {
        if ($dataValidation === null) {
            unset($this->dataValidationCollection[$cellCoordinate]);
        } else {
            $this->dataValidationCollection[$cellCoordinate] = $dataValidation;
        }

        return $this;
    }

    /**
     * Data validation at a specific coordinate exists?
     *
     * @param string $coordinate eg: 'A1'
     */
    public function dataValidationExists($coordinate): bool
    {
        return isset($this->dataValidationCollection[$coordinate]);
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
    public function shrinkRangeToFit($range): string
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
     * @return $this
     */
    public function resetTabColor(): static
    {
        $this->tabColor = null;

        return $this;
    }

    /**
     * Tab color set?
     */
    public function isTabColorSet(): bool
    {
        return $this->tabColor !== null;
    }

    /**
     * Copy worksheet (!= clone!).
     */
    public function copy(): static
    {
        return clone $this;
    }

    /**
     * Returns a boolean true if the specified row contains no cells. By default, this means that no cell records
     *          exist in the collection for this row. false will be returned otherwise.
     *     This rule can be modified by passing a $definitionOfEmptyFlags value:
     *          1 - CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL If the only cells in the collection are null value
     *                  cells, then the row will be considered empty.
     *          2 - CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL If the only cells in the collection are empty
     *                  string value cells, then the row will be considered empty.
     *          3 - CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL | CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL
     *                  If the only cells in the collection are null value or empty string value cells, then the row
     *                  will be considered empty.
     *
     * @param int $definitionOfEmptyFlags
     *              Possible Flag Values are:
     *                  CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL
     *                  CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL
     */
    public function isEmptyRow(int $rowId, int $definitionOfEmptyFlags = 0): bool
    {
        try {
            $iterator = new RowIterator($this, $rowId, $rowId);
            $iterator->seek($rowId);
            $row = $iterator->current();
        } catch (Exception) {
            return true;
        }

        return $row->isEmpty($definitionOfEmptyFlags);
    }

    /**
     * Returns a boolean true if the specified column contains no cells. By default, this means that no cell records
     *          exist in the collection for this column. false will be returned otherwise.
     *     This rule can be modified by passing a $definitionOfEmptyFlags value:
     *          1 - CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL If the only cells in the collection are null value
     *                  cells, then the column will be considered empty.
     *          2 - CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL If the only cells in the collection are empty
     *                  string value cells, then the column will be considered empty.
     *          3 - CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL | CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL
     *                  If the only cells in the collection are null value or empty string value cells, then the column
     *                  will be considered empty.
     *
     * @param int $definitionOfEmptyFlags
     *              Possible Flag Values are:
     *                  CellIterator::TREAT_NULL_VALUE_AS_EMPTY_CELL
     *                  CellIterator::TREAT_EMPTY_STRING_AS_EMPTY_CELL
     */
    public function isEmptyColumn(string $columnId, int $definitionOfEmptyFlags = 0): bool
    {
        try {
            $iterator = new ColumnIterator($this, $columnId, $columnId);
            $iterator->seek($columnId);
            $column = $iterator->current();
        } catch (Exception) {
            return true;
        }

        return $column->isEmpty($definitionOfEmptyFlags);
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        // @phpstan-ignore-next-line
        foreach ($this as $key => $val) {
            if ($key == 'parent') {
                continue;
            }

            if (is_object($val) || (is_array($val))) {
                if ($key == 'cellCollection') {
                    $newCollection = $this->getCellCollection()->cloneCellCollection($this);
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
     * @param string $codeName Same rule as Title minus space not allowed (but, like Excel, change
     *                       silently space to underscore)
     * @param bool $validate False to skip validation of new title. WARNING: This should only be set
     *                       at parse time (by Readers), where titles can be assumed to be valid.
     *
     * @return $this
     */
    public function setCodeName($codeName, $validate = true): static
    {
        // Is this a 'rename' or not?
        if ($this->getCodeName() == $codeName) {
            return $this;
        }

        if ($validate) {
            $codeName = str_replace(' ', '_', $codeName); //Excel does this automatically without flinching, we are doing the same

            // Syntax check
            // throw an exception if not valid
            self::checkSheetCodeName($codeName);

            // We use the same code that setTitle to find a valid codeName else not using a space (Excel don't like) but a '_'

            if ($this->parent !== null) {
                // Is there already such sheet name?
                if ($this->parent->sheetCodeNameExists($codeName)) {
                    // Use name, but append with lowest possible integer

                    if (Shared\StringHelper::countCharacters($codeName) > 29) {
                        $codeName = Shared\StringHelper::substring($codeName, 0, 29);
                    }
                    $i = 1;
                    while ($this->getParentOrThrow()->sheetCodeNameExists($codeName . '_' . $i)) {
                        ++$i;
                        if ($i == 10) {
                            if (Shared\StringHelper::countCharacters($codeName) > 28) {
                                $codeName = Shared\StringHelper::substring($codeName, 0, 28);
                            }
                        } elseif ($i == 100) {
                            if (Shared\StringHelper::countCharacters($codeName) > 27) {
                                $codeName = Shared\StringHelper::substring($codeName, 0, 27);
                            }
                        }
                    }

                    $codeName .= '_' . $i; // ok, we have a valid name
                }
            }
        }

        $this->codeName = $codeName;

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
     */
    public function hasCodeName(): bool
    {
        return $this->codeName !== null;
    }

    public static function nameRequiresQuotes(string $sheetName): bool
    {
        return preg_match(self::SHEET_NAME_REQUIRES_NO_QUOTES, $sheetName) !== 1;
    }

    public function isRowVisible(int $row): bool
    {
        return !$this->rowDimensionExists($row) || $this->getRowDimension($row)->getVisible();
    }

    /**
     * Same as Cell->isLocked, but without creating cell if it doesn't exist.
     */
    public function isCellLocked(string $coordinate): bool
    {
        if ($this->getProtection()->getsheet() !== true) {
            return false;
        }
        if ($this->cellExists($coordinate)) {
            return $this->getCell($coordinate)->isLocked();
        }
        $spreadsheet = $this->parent;
        $xfIndex = $this->getXfIndex($coordinate);
        if ($spreadsheet === null || $xfIndex === null) {
            return true;
        }

        return $spreadsheet->getCellXfByIndex($xfIndex)->getProtection()->getLocked() !== StyleProtection::PROTECTION_UNPROTECTED;
    }

    /**
     * Same as Cell->isHiddenOnFormulaBar, but without creating cell if it doesn't exist.
     */
    public function isCellHiddenOnFormulaBar(string $coordinate): bool
    {
        if ($this->cellExists($coordinate)) {
            return $this->getCell($coordinate)->isHiddenOnFormulaBar();
        }

        // cell doesn't exist, therefore isn't a formula,
        // therefore isn't hidden on formula bar.
        return false;
    }

    private function getXfIndex(string $coordinate): ?int
    {
        [$column, $row] = Coordinate::coordinateFromString($coordinate);
        $row = (int) $row;
        $xfIndex = null;
        if ($this->rowDimensionExists($row)) {
            $xfIndex = $this->getRowDimension($row)->getXfIndex();
        }
        if ($xfIndex === null && $this->ColumnDimensionExists($column)) {
            $xfIndex = $this->getColumnDimension($column)->getXfIndex();
        }

        return $xfIndex;
    }
}
