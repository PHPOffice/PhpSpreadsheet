<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReferenceHelper
{
    /**    Constants                */
    /**    Regular Expressions      */
    const REFHELPER_REGEXP_CELLREF = '((\w*|\'[^!]*\')!)?(?<![:a-z\$])(\$?[a-z]{1,3}\$?\d+)(?=[^:!\d\'])';
    const REFHELPER_REGEXP_CELLRANGE = '((\w*|\'[^!]*\')!)?(\$?[a-z]{1,3}\$?\d+):(\$?[a-z]{1,3}\$?\d+)';
    const REFHELPER_REGEXP_ROWRANGE = '((\w*|\'[^!]*\')!)?(\$?\d+):(\$?\d+)';
    const REFHELPER_REGEXP_COLRANGE = '((\w*|\'[^!]*\')!)?(\$?[a-z]{1,3}):(\$?[a-z]{1,3})';

    /**
     * Instance of this class.
     *
     * @var ?ReferenceHelper
     */
    private static $instance;

    /**
     * @var CellReferenceHelper
     */
    private $cellReferenceHelper;

    /**
     * Get an instance of this class.
     *
     * @return ReferenceHelper
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Create a new ReferenceHelper.
     */
    protected function __construct()
    {
    }

    /**
     * Compare two column addresses
     * Intended for use as a Callback function for sorting column addresses by column.
     *
     * @param string $a First column to test (e.g. 'AA')
     * @param string $b Second column to test (e.g. 'Z')
     *
     * @return int
     */
    public static function columnSort($a, $b)
    {
        return strcasecmp(strlen($a) . $a, strlen($b) . $b);
    }

    /**
     * Compare two column addresses
     * Intended for use as a Callback function for reverse sorting column addresses by column.
     *
     * @param string $a First column to test (e.g. 'AA')
     * @param string $b Second column to test (e.g. 'Z')
     *
     * @return int
     */
    public static function columnReverseSort($a, $b)
    {
        return -strcasecmp(strlen($a) . $a, strlen($b) . $b);
    }

    /**
     * Compare two cell addresses
     * Intended for use as a Callback function for sorting cell addresses by column and row.
     *
     * @param string $a First cell to test (e.g. 'AA1')
     * @param string $b Second cell to test (e.g. 'Z1')
     *
     * @return int
     */
    public static function cellSort($a, $b)
    {
        [$ac, $ar] = sscanf($a, '%[A-Z]%d');
        [$bc, $br] = sscanf($b, '%[A-Z]%d');

        if ($ar === $br) {
            return strcasecmp(strlen($ac) . $ac, strlen($bc) . $bc);
        }

        return ($ar < $br) ? -1 : 1;
    }

    /**
     * Compare two cell addresses
     * Intended for use as a Callback function for sorting cell addresses by column and row.
     *
     * @param string $a First cell to test (e.g. 'AA1')
     * @param string $b Second cell to test (e.g. 'Z1')
     *
     * @return int
     */
    public static function cellReverseSort($a, $b)
    {
        [$ac, $ar] = sscanf($a, '%[A-Z]%d');
        [$bc, $br] = sscanf($b, '%[A-Z]%d');

        if ($ar === $br) {
            return -strcasecmp(strlen($ac) . $ac, strlen($bc) . $bc);
        }

        return ($ar < $br) ? 1 : -1;
    }

    /**
     * Update page breaks when inserting/deleting rows/columns.
     *
     * @param Worksheet $worksheet The worksheet that we're editing
     * @param int $numberOfColumns Number of columns to insert/delete (negative values indicate deletion)
     * @param int $numberOfRows Number of rows to insert/delete (negative values indicate deletion)
     */
    protected function adjustPageBreaks(Worksheet $worksheet, $numberOfColumns, $numberOfRows): void
    {
        $aBreaks = $worksheet->getBreaks();
        ($numberOfColumns > 0 || $numberOfRows > 0)
            ? uksort($aBreaks, [self::class, 'cellReverseSort'])
            : uksort($aBreaks, [self::class, 'cellSort']);

        foreach ($aBreaks as $cellAddress => $value) {
            if ($this->cellReferenceHelper->cellAddressInDeleteRange($cellAddress) === true) {
                //    If we're deleting, then clear any defined breaks that are within the range
                //        of rows/columns that we're deleting
                $worksheet->setBreak($cellAddress, Worksheet::BREAK_NONE);
            } else {
                //    Otherwise update any affected breaks by inserting a new break at the appropriate point
                //        and removing the old affected break
                $newReference = $this->updateCellReference($cellAddress);
                if ($cellAddress !== $newReference) {
                    $worksheet->setBreak($newReference, $value)
                        ->setBreak($cellAddress, Worksheet::BREAK_NONE);
                }
            }
        }
    }

    /**
     * Update cell comments when inserting/deleting rows/columns.
     *
     * @param Worksheet $worksheet The worksheet that we're editing
     */
    protected function adjustComments($worksheet): void
    {
        $aComments = $worksheet->getComments();
        $aNewComments = []; // the new array of all comments

        foreach ($aComments as $cellAddress => &$value) {
            // Any comments inside a deleted range will be ignored
            if ($this->cellReferenceHelper->cellAddressInDeleteRange($cellAddress) === false) {
                // Otherwise build a new array of comments indexed by the adjusted cell reference
                $newReference = $this->updateCellReference($cellAddress);
                $aNewComments[$newReference] = $value;
            }
        }
        //    Replace the comments array with the new set of comments
        $worksheet->setComments($aNewComments);
    }

    /**
     * Update hyperlinks when inserting/deleting rows/columns.
     *
     * @param Worksheet $worksheet The worksheet that we're editing
     * @param int $numberOfColumns Number of columns to insert/delete (negative values indicate deletion)
     * @param int $numberOfRows Number of rows to insert/delete (negative values indicate deletion)
     */
    protected function adjustHyperlinks($worksheet, $numberOfColumns, $numberOfRows): void
    {
        $aHyperlinkCollection = $worksheet->getHyperlinkCollection();
        ($numberOfColumns > 0 || $numberOfRows > 0)
            ? uksort($aHyperlinkCollection, [self::class, 'cellReverseSort'])
            : uksort($aHyperlinkCollection, [self::class, 'cellSort']);

        foreach ($aHyperlinkCollection as $cellAddress => $value) {
            $newReference = $this->updateCellReference($cellAddress);
            if ($this->cellReferenceHelper->cellAddressInDeleteRange($cellAddress) === true) {
                $worksheet->setHyperlink($cellAddress, null);
            } elseif ($cellAddress !== $newReference) {
                $worksheet->setHyperlink($newReference, $value);
                $worksheet->setHyperlink($cellAddress, null);
            }
        }
    }

    /**
     * Update conditional formatting styles when inserting/deleting rows/columns.
     *
     * @param Worksheet $worksheet The worksheet that we're editing
     * @param int $numberOfColumns Number of columns to insert/delete (negative values indicate deletion)
     * @param int $numberOfRows Number of rows to insert/delete (negative values indicate deletion)
     */
    protected function adjustConditionalFormatting($worksheet, $numberOfColumns, $numberOfRows): void
    {
        $aStyles = $worksheet->getConditionalStylesCollection();
        ($numberOfColumns > 0 || $numberOfRows > 0)
            ? uksort($aStyles, [self::class, 'cellReverseSort'])
            : uksort($aStyles, [self::class, 'cellSort']);

        foreach ($aStyles as $cellAddress => $cfRules) {
            $worksheet->removeConditionalStyles($cellAddress);
            $newReference = $this->updateCellReference($cellAddress);

            foreach ($cfRules as &$cfRule) {
                /** @var Conditional $cfRule */
                $conditions = $cfRule->getConditions();
                foreach ($conditions as &$condition) {
                    if (is_string($condition)) {
                        $condition = $this->updateFormulaReferences(
                            $condition,
                            $this->cellReferenceHelper->beforeCellAddress(),
                            $numberOfColumns,
                            $numberOfRows,
                            $worksheet->getTitle(),
                            true
                        );
                    }
                }
                $cfRule->setConditions($conditions);
            }
            $worksheet->setConditionalStyles($newReference, $cfRules);
        }
    }

    /**
     * Update data validations when inserting/deleting rows/columns.
     *
     * @param Worksheet $worksheet The worksheet that we're editing
     * @param int $numberOfColumns Number of columns to insert/delete (negative values indicate deletion)
     * @param int $numberOfRows Number of rows to insert/delete (negative values indicate deletion)
     */
    protected function adjustDataValidations(Worksheet $worksheet, $numberOfColumns, $numberOfRows): void
    {
        $aDataValidationCollection = $worksheet->getDataValidationCollection();
        ($numberOfColumns > 0 || $numberOfRows > 0)
            ? uksort($aDataValidationCollection, [self::class, 'cellReverseSort'])
            : uksort($aDataValidationCollection, [self::class, 'cellSort']);

        foreach ($aDataValidationCollection as $cellAddress => $value) {
            $newReference = $this->updateCellReference($cellAddress);
            if ($cellAddress !== $newReference) {
                $worksheet->setDataValidation($newReference, $value);
                $worksheet->setDataValidation($cellAddress, null);
            }
        }
    }

    /**
     * Update merged cells when inserting/deleting rows/columns.
     *
     * @param Worksheet $worksheet The worksheet that we're editing
     */
    protected function adjustMergeCells(Worksheet $worksheet): void
    {
        $aMergeCells = $worksheet->getMergeCells();
        $aNewMergeCells = []; // the new array of all merge cells
        foreach ($aMergeCells as $cellAddress => &$value) {
            $newReference = $this->updateCellReference($cellAddress);
            $aNewMergeCells[$newReference] = $newReference;
        }
        $worksheet->setMergeCells($aNewMergeCells); // replace the merge cells array
    }

    /**
     * Update protected cells when inserting/deleting rows/columns.
     *
     * @param Worksheet $worksheet The worksheet that we're editing
     * @param int $numberOfColumns Number of columns to insert/delete (negative values indicate deletion)
     * @param int $numberOfRows Number of rows to insert/delete (negative values indicate deletion)
     */
    protected function adjustProtectedCells(Worksheet $worksheet, $numberOfColumns, $numberOfRows): void
    {
        $aProtectedCells = $worksheet->getProtectedCells();
        ($numberOfColumns > 0 || $numberOfRows > 0)
            ? uksort($aProtectedCells, [self::class, 'cellReverseSort'])
            : uksort($aProtectedCells, [self::class, 'cellSort']);
        foreach ($aProtectedCells as $cellAddress => $value) {
            $newReference = $this->updateCellReference($cellAddress);
            if ($cellAddress !== $newReference) {
                $worksheet->protectCells($newReference, $value, true);
                $worksheet->unprotectCells($cellAddress);
            }
        }
    }

    /**
     * Update column dimensions when inserting/deleting rows/columns.
     *
     * @param Worksheet $worksheet The worksheet that we're editing
     */
    protected function adjustColumnDimensions(Worksheet $worksheet): void
    {
        $aColumnDimensions = array_reverse($worksheet->getColumnDimensions(), true);
        if (!empty($aColumnDimensions)) {
            foreach ($aColumnDimensions as $objColumnDimension) {
                $newReference = $this->updateCellReference($objColumnDimension->getColumnIndex() . '1');
                [$newReference] = Coordinate::coordinateFromString($newReference);
                if ($objColumnDimension->getColumnIndex() !== $newReference) {
                    $objColumnDimension->setColumnIndex($newReference);
                }
            }
            $worksheet->refreshColumnDimensions();
        }
    }

    /**
     * Update row dimensions when inserting/deleting rows/columns.
     *
     * @param Worksheet $worksheet The worksheet that we're editing
     * @param int $beforeRow Number of the row we're inserting/deleting before
     * @param int $numberOfRows Number of rows to insert/delete (negative values indicate deletion)
     */
    protected function adjustRowDimensions(Worksheet $worksheet, $beforeRow, $numberOfRows): void
    {
        $aRowDimensions = array_reverse($worksheet->getRowDimensions(), true);
        if (!empty($aRowDimensions)) {
            foreach ($aRowDimensions as $objRowDimension) {
                $newReference = $this->updateCellReference('A' . $objRowDimension->getRowIndex());
                [, $newReference] = Coordinate::coordinateFromString($newReference);
                $newRoweference = (int) $newReference;
                if ($objRowDimension->getRowIndex() !== $newRoweference) {
                    $objRowDimension->setRowIndex($newRoweference);
                }
            }
            $worksheet->refreshRowDimensions();

            $copyDimension = $worksheet->getRowDimension($beforeRow - 1);
            for ($i = $beforeRow; $i <= $beforeRow - 1 + $numberOfRows; ++$i) {
                $newDimension = $worksheet->getRowDimension($i);
                $newDimension->setRowHeight($copyDimension->getRowHeight());
                $newDimension->setVisible($copyDimension->getVisible());
                $newDimension->setOutlineLevel($copyDimension->getOutlineLevel());
                $newDimension->setCollapsed($copyDimension->getCollapsed());
            }
        }
    }

    /**
     * Insert a new column or row, updating all possible related data.
     *
     * @param string $beforeCellAddress Insert before this cell address (e.g. 'A1')
     * @param int $numberOfColumns Number of columns to insert/delete (negative values indicate deletion)
     * @param int $numberOfRows Number of rows to insert/delete (negative values indicate deletion)
     * @param Worksheet $worksheet The worksheet that we're editing
     */
    public function insertNewBefore(
        string $beforeCellAddress,
        int $numberOfColumns,
        int $numberOfRows,
        Worksheet $worksheet
    ): void {
        $remove = ($numberOfColumns < 0 || $numberOfRows < 0);
        $allCoordinates = $worksheet->getCoordinates();

        if (
            $this->cellReferenceHelper === null ||
            $this->cellReferenceHelper->refreshRequired($beforeCellAddress, $numberOfColumns, $numberOfRows)
        ) {
            $this->cellReferenceHelper = new CellReferenceHelper($beforeCellAddress, $numberOfColumns, $numberOfRows);
        }

        // Get coordinate of $beforeCellAddress
        [$beforeColumn, $beforeRow] = Coordinate::indexesFromString($beforeCellAddress);

        // Clear cells if we are removing columns or rows
        $highestColumn = $worksheet->getHighestColumn();
        $highestRow = $worksheet->getHighestRow();

        // 1. Clear column strips if we are removing columns
        if ($numberOfColumns < 0 && $beforeColumn - 2 + $numberOfColumns > 0) {
            $this->clearColumnStrips($highestRow, $beforeColumn, $numberOfColumns, $worksheet);
        }

        // 2. Clear row strips if we are removing rows
        if ($numberOfRows < 0 && $beforeRow - 1 + $numberOfRows > 0) {
            $this->clearRowStrips($highestColumn, $beforeColumn, $beforeRow, $numberOfRows, $worksheet);
        }

        // Find missing coordinates. This is important when inserting column before the last column
        $missingCoordinates = array_filter(
            array_map(function ($row) use ($highestColumn) {
                return $highestColumn . $row;
            }, range(1, $highestRow)),
            function ($coordinate) use ($allCoordinates) {
                return !in_array($coordinate, $allCoordinates);
            }
        );

        // Create missing cells with null values
        if (!empty($missingCoordinates)) {
            foreach ($missingCoordinates as $coordinate) {
                $worksheet->createNewCell($coordinate);
            }

            // Refresh all coordinates
            $allCoordinates = $worksheet->getCoordinates();
        }

        // Loop through cells, bottom-up, and change cell coordinate
        if ($remove) {
            // It's faster to reverse and pop than to use unshift, especially with large cell collections
            $allCoordinates = array_reverse($allCoordinates);
        }
        while ($coordinate = array_pop($allCoordinates)) {
            $cell = $worksheet->getCell($coordinate);
            $cellIndex = Coordinate::columnIndexFromString($cell->getColumn());

            if ($cellIndex - 1 + $numberOfColumns < 0) {
                continue;
            }

            // New coordinate
            $newCoordinate = Coordinate::stringFromColumnIndex($cellIndex + $numberOfColumns) . ($cell->getRow() + $numberOfRows);

            // Should the cell be updated? Move value and cellXf index from one cell to another.
            if (($cellIndex >= $beforeColumn) && ($cell->getRow() >= $beforeRow)) {
                // Update cell styles
                $worksheet->getCell($newCoordinate)->setXfIndex($cell->getXfIndex());

                // Insert this cell at its new location
                if ($cell->getDataType() === DataType::TYPE_FORMULA) {
                    // Formula should be adjusted
                    $worksheet->getCell($newCoordinate)
                        ->setValue($this->updateFormulaReferences($cell->getValue(), $beforeCellAddress, $numberOfColumns, $numberOfRows, $worksheet->getTitle()));
                } else {
                    // Formula should not be adjusted
                    $worksheet->getCell($newCoordinate)->setValueExplicit($cell->getValue(), $cell->getDataType());
                }

                // Clear the original cell
                $worksheet->getCellCollection()->delete($coordinate);
            } else {
                /*    We don't need to update styles for rows/columns before our insertion position,
                        but we do still need to adjust any formulae    in those cells                    */
                if ($cell->getDataType() === DataType::TYPE_FORMULA) {
                    // Formula should be adjusted
                    $cell->setValue($this->updateFormulaReferences($cell->getValue(), $beforeCellAddress, $numberOfColumns, $numberOfRows, $worksheet->getTitle()));
                }
            }
        }

        // Duplicate styles for the newly inserted cells
        $highestColumn = $worksheet->getHighestColumn();
        $highestRow = $worksheet->getHighestRow();

        if ($numberOfColumns > 0 && $beforeColumn - 2 > 0) {
            $this->duplicateStylesByColumn($worksheet, $beforeColumn, $beforeRow, $highestRow, $numberOfColumns);
        }

        if ($numberOfRows > 0 && $beforeRow - 1 > 0) {
            $this->duplicateStylesByRow($worksheet, $beforeColumn, $beforeRow, $highestColumn, $numberOfRows);
        }

        // Update worksheet: column dimensions
        $this->adjustColumnDimensions($worksheet);

        // Update worksheet: row dimensions
        $this->adjustRowDimensions($worksheet, $beforeRow, $numberOfRows);

        //    Update worksheet: page breaks
        $this->adjustPageBreaks($worksheet, $numberOfColumns, $numberOfRows);

        //    Update worksheet: comments
        $this->adjustComments($worksheet);

        // Update worksheet: hyperlinks
        $this->adjustHyperlinks($worksheet, $numberOfColumns, $numberOfRows);

        // Update worksheet: conditional formatting styles
        $this->adjustConditionalFormatting($worksheet, $numberOfColumns, $numberOfRows);

        // Update worksheet: data validations
        $this->adjustDataValidations($worksheet, $numberOfColumns, $numberOfRows);

        // Update worksheet: merge cells
        $this->adjustMergeCells($worksheet);

        // Update worksheet: protected cells
        $this->adjustProtectedCells($worksheet, $numberOfColumns, $numberOfRows);

        // Update worksheet: autofilter
        $this->adjustAutoFilter($worksheet, $beforeCellAddress, $numberOfColumns);

        // Update worksheet: freeze pane
        if ($worksheet->getFreezePane()) {
            $splitCell = $worksheet->getFreezePane() ?? '';
            $topLeftCell = $worksheet->getTopLeftCell() ?? '';

            $splitCell = $this->updateCellReference($splitCell);
            $topLeftCell = $this->updateCellReference($topLeftCell);

            $worksheet->freezePane($splitCell, $topLeftCell);
        }

        // Page setup
        if ($worksheet->getPageSetup()->isPrintAreaSet()) {
            $worksheet->getPageSetup()->setPrintArea(
                $this->updateCellReference($worksheet->getPageSetup()->getPrintArea())
            );
        }

        // Update worksheet: drawings
        $aDrawings = $worksheet->getDrawingCollection();
        foreach ($aDrawings as $objDrawing) {
            $newReference = $this->updateCellReference($objDrawing->getCoordinates());
            if ($objDrawing->getCoordinates() != $newReference) {
                $objDrawing->setCoordinates($newReference);
            }
        }

        // Update workbook: define names
        if (count($worksheet->getParent()->getDefinedNames()) > 0) {
            foreach ($worksheet->getParent()->getDefinedNames() as $definedName) {
                if ($definedName->getWorksheet() !== null && $definedName->getWorksheet()->getHashCode() === $worksheet->getHashCode()) {
                    $definedName->setValue($this->updateCellReference($definedName->getValue()));
                }
            }
        }

        // Garbage collect
        $worksheet->garbageCollect();
    }

    /**
     * Update references within formulas.
     *
     * @param string $formula Formula to update
     * @param string $beforeCellAddress Insert before this one
     * @param int $numberOfColumns Number of columns to insert
     * @param int $numberOfRows Number of rows to insert
     * @param string $worksheetName Worksheet name/title
     *
     * @return string Updated formula
     */
    public function updateFormulaReferences(
        $formula = '',
        $beforeCellAddress = 'A1',
        $numberOfColumns = 0,
        $numberOfRows = 0,
        $worksheetName = '',
        bool $includeAbsoluteReferences = false
    ) {
        if (
            $this->cellReferenceHelper === null ||
            $this->cellReferenceHelper->refreshRequired($beforeCellAddress, $numberOfColumns, $numberOfRows)
        ) {
            $this->cellReferenceHelper = new CellReferenceHelper($beforeCellAddress, $numberOfColumns, $numberOfRows);
        }

        //    Update cell references in the formula
        $formulaBlocks = explode('"', $formula);
        $i = false;
        foreach ($formulaBlocks as &$formulaBlock) {
            //    Ignore blocks that were enclosed in quotes (alternating entries in the $formulaBlocks array after the explode)
            if ($i = !$i) {
                $adjustCount = 0;
                $newCellTokens = $cellTokens = [];
                //    Search for row ranges (e.g. 'Sheet1'!3:5 or 3:5) with or without $ absolutes (e.g. $3:5)
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_ROWRANGE . '/mui', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);
                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3] . ':' . $match[4];
                        $modified3 = substr($this->updateCellReference('$A' . $match[3], $includeAbsoluteReferences), 2);
                        $modified4 = substr($this->updateCellReference('$A' . $match[4], $includeAbsoluteReferences), 2);

                        if ($match[3] . ':' . $match[4] !== $modified3 . ':' . $modified4) {
                            if (($match[2] == '') || (trim($match[2], "'") == $worksheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3 . ':' . $modified4;
                                //    Max worksheet size is 1,048,576 rows by 16,384 columns in Excel 2007, so our adjustments need to be at least one digit more
                                $column = 100000;
                                $row = 10000000 + (int) trim($match[3], '$');
                                $cellIndex = $column . $row;

                                $newCellTokens[$cellIndex] = preg_quote($toString, '/');
                                $cellTokens[$cellIndex] = '/(?<!\d\$\!)' . preg_quote($fromString, '/') . '(?!\d)/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                //    Search for column ranges (e.g. 'Sheet1'!C:E or C:E) with or without $ absolutes (e.g. $C:E)
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_COLRANGE . '/mui', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);
                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3] . ':' . $match[4];
                        $modified3 = substr($this->updateCellReference($match[3] . '$1', $includeAbsoluteReferences), 0, -2);
                        $modified4 = substr($this->updateCellReference($match[4] . '$1', $includeAbsoluteReferences), 0, -2);

                        if ($match[3] . ':' . $match[4] !== $modified3 . ':' . $modified4) {
                            if (($match[2] == '') || (trim($match[2], "'") == $worksheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3 . ':' . $modified4;
                                //    Max worksheet size is 1,048,576 rows by 16,384 columns in Excel 2007, so our adjustments need to be at least one digit more
                                $column = Coordinate::columnIndexFromString(trim($match[3], '$')) + 100000;
                                $row = 10000000;
                                $cellIndex = $column . $row;

                                $newCellTokens[$cellIndex] = preg_quote($toString, '/');
                                $cellTokens[$cellIndex] = '/(?<![A-Z\$\!])' . preg_quote($fromString, '/') . '(?![A-Z])/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                //    Search for cell ranges (e.g. 'Sheet1'!A3:C5 or A3:C5) with or without $ absolutes (e.g. $A1:C$5)
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_CELLRANGE . '/mui', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);
                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3] . ':' . $match[4];
                        $modified3 = $this->updateCellReference($match[3], $includeAbsoluteReferences);
                        $modified4 = $this->updateCellReference($match[4], $includeAbsoluteReferences);

                        if ($match[3] . $match[4] !== $modified3 . $modified4) {
                            if (($match[2] == '') || (trim($match[2], "'") == $worksheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3 . ':' . $modified4;
                                [$column, $row] = Coordinate::coordinateFromString($match[3]);
                                //    Max worksheet size is 1,048,576 rows by 16,384 columns in Excel 2007, so our adjustments need to be at least one digit more
                                $column = Coordinate::columnIndexFromString(trim($column, '$')) + 100000;
                                $row = (int) trim($row, '$') + 10000000;
                                $cellIndex = $column . $row;

                                $newCellTokens[$cellIndex] = preg_quote($toString, '/');
                                $cellTokens[$cellIndex] = '/(?<![A-Z]\$\!)' . preg_quote($fromString, '/') . '(?!\d)/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                //    Search for cell references (e.g. 'Sheet1'!A3 or C5) with or without $ absolutes (e.g. $A1 or C$5)
                $matchCount = preg_match_all('/' . self::REFHELPER_REGEXP_CELLREF . '/mui', ' ' . $formulaBlock . ' ', $matches, PREG_SET_ORDER);

                if ($matchCount > 0) {
                    foreach ($matches as $match) {
                        $fromString = ($match[2] > '') ? $match[2] . '!' : '';
                        $fromString .= $match[3];

                        $modified3 = $this->updateCellReference($match[3], $includeAbsoluteReferences);
                        if ($match[3] !== $modified3) {
                            if (($match[2] == '') || (trim($match[2], "'") == $worksheetName)) {
                                $toString = ($match[2] > '') ? $match[2] . '!' : '';
                                $toString .= $modified3;
                                [$column, $row] = Coordinate::coordinateFromString($match[3]);
                                $columnAdditionalIndex = $column[0] === '$' ? 1 : 0;
                                $rowAdditionalIndex = $row[0] === '$' ? 1 : 0;
                                //    Max worksheet size is 1,048,576 rows by 16,384 columns in Excel 2007, so our adjustments need to be at least one digit more
                                $column = Coordinate::columnIndexFromString(trim($column, '$')) + 100000;
                                $row = (int) trim($row, '$') + 10000000;
                                $cellIndex = $row . $rowAdditionalIndex . $column . $columnAdditionalIndex;

                                $newCellTokens[$cellIndex] = preg_quote($toString, '/');
                                $cellTokens[$cellIndex] = '/(?<![A-Z\$\!])' . preg_quote($fromString, '/') . '(?!\d)/i';
                                ++$adjustCount;
                            }
                        }
                    }
                }
                if ($adjustCount > 0) {
                    if ($numberOfColumns > 0 || $numberOfRows > 0) {
                        krsort($cellTokens);
                        krsort($newCellTokens);
                    } else {
                        ksort($cellTokens);
                        ksort($newCellTokens);
                    }   //  Update cell references in the formula
                    $formulaBlock = str_replace('\\', '', preg_replace($cellTokens, $newCellTokens, $formulaBlock));
                }
            }
        }
        unset($formulaBlock);

        //    Then rebuild the formula string
        return implode('"', $formulaBlocks);
    }

    /**
     * Update all cell references within a formula, irrespective of worksheet.
     */
    public function updateFormulaReferencesAnyWorksheet(string $formula = '', int $numberOfColumns = 0, int $numberOfRows = 0): string
    {
        $formula = $this->updateCellReferencesAllWorksheets($formula, $numberOfColumns, $numberOfRows);

        if ($numberOfColumns !== 0) {
            $formula = $this->updateColumnRangesAllWorksheets($formula, $numberOfColumns);
        }

        if ($numberOfRows !== 0) {
            $formula = $this->updateRowRangesAllWorksheets($formula, $numberOfRows);
        }

        return $formula;
    }

    private function updateCellReferencesAllWorksheets(string $formula, int $numberOfColumns, int $numberOfRows): string
    {
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_CELLREF_RELATIVE . '/mui',
            $formula,
            $splitRanges,
            PREG_OFFSET_CAPTURE
        );

        $columnLengths = array_map('strlen', array_column($splitRanges[6], 0));
        $rowLengths = array_map('strlen', array_column($splitRanges[7], 0));
        $columnOffsets = array_column($splitRanges[6], 1);
        $rowOffsets = array_column($splitRanges[7], 1);

        $columns = $splitRanges[6];
        $rows = $splitRanges[7];

        while ($splitCount > 0) {
            --$splitCount;
            $columnLength = $columnLengths[$splitCount];
            $rowLength = $rowLengths[$splitCount];
            $columnOffset = $columnOffsets[$splitCount];
            $rowOffset = $rowOffsets[$splitCount];
            $column = $columns[$splitCount][0];
            $row = $rows[$splitCount][0];

            if (!empty($column) && $column[0] !== '$') {
                $column = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($column) + $numberOfColumns);
                $formula = substr($formula, 0, $columnOffset) . $column . substr($formula, $columnOffset + $columnLength);
            }
            if (!empty($row) && $row[0] !== '$') {
                $row += $numberOfRows;
                $formula = substr($formula, 0, $rowOffset) . $row . substr($formula, $rowOffset + $rowLength);
            }
        }

        return $formula;
    }

    private function updateColumnRangesAllWorksheets(string $formula, int $numberOfColumns): string
    {
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_COLUMNRANGE_RELATIVE . '/mui',
            $formula,
            $splitRanges,
            PREG_OFFSET_CAPTURE
        );

        $fromColumnLengths = array_map('strlen', array_column($splitRanges[1], 0));
        $fromColumnOffsets = array_column($splitRanges[1], 1);
        $toColumnLengths = array_map('strlen', array_column($splitRanges[2], 0));
        $toColumnOffsets = array_column($splitRanges[2], 1);

        $fromColumns = $splitRanges[1];
        $toColumns = $splitRanges[2];

        while ($splitCount > 0) {
            --$splitCount;
            $fromColumnLength = $fromColumnLengths[$splitCount];
            $toColumnLength = $toColumnLengths[$splitCount];
            $fromColumnOffset = $fromColumnOffsets[$splitCount];
            $toColumnOffset = $toColumnOffsets[$splitCount];
            $fromColumn = $fromColumns[$splitCount][0];
            $toColumn = $toColumns[$splitCount][0];

            if (!empty($fromColumn) && $fromColumn[0] !== '$') {
                $fromColumn = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($fromColumn) + $numberOfColumns);
                $formula = substr($formula, 0, $fromColumnOffset) . $fromColumn . substr($formula, $fromColumnOffset + $fromColumnLength);
            }
            if (!empty($toColumn) && $toColumn[0] !== '$') {
                $toColumn = Coordinate::stringFromColumnIndex(Coordinate::columnIndexFromString($toColumn) + $numberOfColumns);
                $formula = substr($formula, 0, $toColumnOffset) . $toColumn . substr($formula, $toColumnOffset + $toColumnLength);
            }
        }

        return $formula;
    }

    private function updateRowRangesAllWorksheets(string $formula, int $numberOfRows): string
    {
        $splitCount = preg_match_all(
            '/' . Calculation::CALCULATION_REGEXP_ROWRANGE_RELATIVE . '/mui',
            $formula,
            $splitRanges,
            PREG_OFFSET_CAPTURE
        );

        $fromRowLengths = array_map('strlen', array_column($splitRanges[1], 0));
        $fromRowOffsets = array_column($splitRanges[1], 1);
        $toRowLengths = array_map('strlen', array_column($splitRanges[2], 0));
        $toRowOffsets = array_column($splitRanges[2], 1);

        $fromRows = $splitRanges[1];
        $toRows = $splitRanges[2];

        while ($splitCount > 0) {
            --$splitCount;
            $fromRowLength = $fromRowLengths[$splitCount];
            $toRowLength = $toRowLengths[$splitCount];
            $fromRowOffset = $fromRowOffsets[$splitCount];
            $toRowOffset = $toRowOffsets[$splitCount];
            $fromRow = $fromRows[$splitCount][0];
            $toRow = $toRows[$splitCount][0];

            if (!empty($fromRow) && $fromRow[0] !== '$') {
                $fromRow += $numberOfRows;
                $formula = substr($formula, 0, $fromRowOffset) . $fromRow . substr($formula, $fromRowOffset + $fromRowLength);
            }
            if (!empty($toRow) && $toRow[0] !== '$') {
                $toRow += $numberOfRows;
                $formula = substr($formula, 0, $toRowOffset) . $toRow . substr($formula, $toRowOffset + $toRowLength);
            }
        }

        return $formula;
    }

    /**
     * Update cell reference.
     *
     * @param string $cellReference Cell address or range of addresses
     *
     * @return string Updated cell range
     */
    private function updateCellReference($cellReference = 'A1', bool $includeAbsoluteReferences = false)
    {
        // Is it in another worksheet? Will not have to update anything.
        if (strpos($cellReference, '!') !== false) {
            return $cellReference;
        // Is it a range or a single cell?
        } elseif (!Coordinate::coordinateIsRange($cellReference)) {
            // Single cell
            return $this->cellReferenceHelper->updateCellReference($cellReference, $includeAbsoluteReferences);
        } elseif (Coordinate::coordinateIsRange($cellReference)) {
            // Range
            return $this->updateCellRange($cellReference, $includeAbsoluteReferences);
        }

        // Return original
        return $cellReference;
    }

    /**
     * Update named formulas (i.e. containing worksheet references / named ranges).
     *
     * @param Spreadsheet $spreadsheet Object to update
     * @param string $oldName Old name (name to replace)
     * @param string $newName New name
     */
    public function updateNamedFormulas(Spreadsheet $spreadsheet, $oldName = '', $newName = ''): void
    {
        if ($oldName == '') {
            return;
        }

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            foreach ($sheet->getCoordinates(false) as $coordinate) {
                $cell = $sheet->getCell($coordinate);
                if (($cell !== null) && ($cell->getDataType() === DataType::TYPE_FORMULA)) {
                    $formula = $cell->getValue();
                    if (strpos($formula, $oldName) !== false) {
                        $formula = str_replace("'" . $oldName . "'!", "'" . $newName . "'!", $formula);
                        $formula = str_replace($oldName . '!', $newName . '!', $formula);
                        $cell->setValueExplicit($formula, DataType::TYPE_FORMULA);
                    }
                }
            }
        }
    }

    /**
     * Update cell range.
     *
     * @param string $cellRange Cell range    (e.g. 'B2:D4', 'B:C' or '2:3')
     *
     * @return string Updated cell range
     */
    private function updateCellRange(string $cellRange = 'A1:A1', bool $includeAbsoluteReferences = false): string
    {
        if (!Coordinate::coordinateIsRange($cellRange)) {
            throw new Exception('Only cell ranges may be passed to this method.');
        }

        // Update range
        $range = Coordinate::splitRange($cellRange);
        $ic = count($range);
        for ($i = 0; $i < $ic; ++$i) {
            $jc = count($range[$i]);
            for ($j = 0; $j < $jc; ++$j) {
                if (ctype_alpha($range[$i][$j])) {
                    $range[$i][$j] = Coordinate::coordinateFromString(
                        $this->cellReferenceHelper->updateCellReference($range[$i][$j] . '1', $includeAbsoluteReferences)
                    )[0];
                } elseif (ctype_digit($range[$i][$j])) {
                    $range[$i][$j] = Coordinate::coordinateFromString(
                        $this->cellReferenceHelper->updateCellReference('A' . $range[$i][$j], $includeAbsoluteReferences)
                    )[1];
                } else {
                    $range[$i][$j] = $this->cellReferenceHelper->updateCellReference($range[$i][$j], $includeAbsoluteReferences);
                }
            }
        }

        // Recreate range string
        return Coordinate::buildRange($range);
    }

    private function clearColumnStrips(int $highestRow, int $beforeColumn, int $numberOfColumns, Worksheet $worksheet): void
    {
        for ($i = 1; $i <= $highestRow - 1; ++$i) {
            for ($j = $beforeColumn - 1 + $numberOfColumns; $j <= $beforeColumn - 2; ++$j) {
                $coordinate = Coordinate::stringFromColumnIndex($j + 1) . $i;
                $worksheet->removeConditionalStyles($coordinate);
                if ($worksheet->cellExists($coordinate)) {
                    $worksheet->getCell($coordinate)->setValueExplicit('', DataType::TYPE_NULL);
                    $worksheet->getCell($coordinate)->setXfIndex(0);
                }
            }
        }
    }

    private function clearRowStrips(string $highestColumn, int $beforeColumn, int $beforeRow, int $numberOfRows, Worksheet $worksheet): void
    {
        $lastColumnIndex = Coordinate::columnIndexFromString($highestColumn) - 1;

        for ($i = $beforeColumn - 1; $i <= $lastColumnIndex; ++$i) {
            for ($j = $beforeRow + $numberOfRows; $j <= $beforeRow - 1; ++$j) {
                $coordinate = Coordinate::stringFromColumnIndex($i + 1) . $j;
                $worksheet->removeConditionalStyles($coordinate);
                if ($worksheet->cellExists($coordinate)) {
                    $worksheet->getCell($coordinate)->setValueExplicit('', DataType::TYPE_NULL);
                    $worksheet->getCell($coordinate)->setXfIndex(0);
                }
            }
        }
    }

    private function adjustAutoFilter(Worksheet $worksheet, string $beforeCellAddress, int $numberOfColumns): void
    {
        $autoFilter = $worksheet->getAutoFilter();
        $autoFilterRange = $autoFilter->getRange();
        if (!empty($autoFilterRange)) {
            if ($numberOfColumns !== 0) {
                $autoFilterColumns = $autoFilter->getColumns();
                if (count($autoFilterColumns) > 0) {
                    $column = '';
                    $row = 0;
                    sscanf($beforeCellAddress, '%[A-Z]%d', $column, $row);
                    $columnIndex = Coordinate::columnIndexFromString($column);
                    [$rangeStart, $rangeEnd] = Coordinate::rangeBoundaries($autoFilterRange);
                    if ($columnIndex <= $rangeEnd[0]) {
                        if ($numberOfColumns < 0) {
                            $this->adjustAutoFilterDeleteRules($columnIndex, $numberOfColumns, $autoFilterColumns, $autoFilter);
                        }
                        $startCol = ($columnIndex > $rangeStart[0]) ? $columnIndex : $rangeStart[0];

                        //    Shuffle columns in autofilter range
                        if ($numberOfColumns > 0) {
                            $this->adjustAutoFilterInsert($startCol, $numberOfColumns, $rangeEnd[0], $autoFilter);
                        } else {
                            $this->adjustAutoFilterDelete($startCol, $numberOfColumns, $rangeEnd[0], $autoFilter);
                        }
                    }
                }
            }

            $worksheet->setAutoFilter(
                $this->updateCellReference($autoFilterRange)
            );
        }
    }

    private function adjustAutoFilterDeleteRules(int $columnIndex, int $numberOfColumns, array $autoFilterColumns, AutoFilter $autoFilter): void
    {
        // If we're actually deleting any columns that fall within the autofilter range,
        //    then we delete any rules for those columns
        $deleteColumn = $columnIndex + $numberOfColumns - 1;
        $deleteCount = abs($numberOfColumns);

        for ($i = 1; $i <= $deleteCount; ++$i) {
            $columnName = Coordinate::stringFromColumnIndex($deleteColumn + 1);
            if (isset($autoFilterColumns[$columnName])) {
                $autoFilter->clearColumn($columnName);
            }
            ++$deleteColumn;
        }
    }

    private function adjustAutoFilterInsert(int $startCol, int $numberOfColumns, int $rangeEnd, AutoFilter $autoFilter): void
    {
        $startColRef = $startCol;
        $endColRef = $rangeEnd;
        $toColRef = $rangeEnd + $numberOfColumns;

        do {
            $autoFilter->shiftColumn(Coordinate::stringFromColumnIndex($endColRef), Coordinate::stringFromColumnIndex($toColRef));
            --$endColRef;
            --$toColRef;
        } while ($startColRef <= $endColRef);
    }

    private function adjustAutoFilterDelete(int $startCol, int $numberOfColumns, int $rangeEnd, AutoFilter $autoFilter): void
    {
        // For delete, we shuffle from beginning to end to avoid overwriting
        $startColID = Coordinate::stringFromColumnIndex($startCol);
        $toColID = Coordinate::stringFromColumnIndex($startCol + $numberOfColumns);
        $endColID = Coordinate::stringFromColumnIndex($rangeEnd + 1);

        do {
            $autoFilter->shiftColumn($startColID, $toColID);
            ++$startColID;
            ++$toColID;
        } while ($startColID !== $endColID);
    }

    private function duplicateStylesByColumn(Worksheet $worksheet, int $beforeColumn, int $beforeRow, int $highestRow, int $numberOfColumns): void
    {
        $beforeColumnName = Coordinate::stringFromColumnIndex($beforeColumn - 1);
        for ($i = $beforeRow; $i <= $highestRow - 1; ++$i) {
            // Style
            $coordinate = $beforeColumnName . $i;
            if ($worksheet->cellExists($coordinate)) {
                $xfIndex = $worksheet->getCell($coordinate)->getXfIndex();
                for ($j = $beforeColumn; $j <= $beforeColumn - 1 + $numberOfColumns; ++$j) {
                    $worksheet->getCellByColumnAndRow($j, $i)->setXfIndex($xfIndex);
                }
            }
        }
    }

    private function duplicateStylesByRow(Worksheet $worksheet, int $beforeColumn, int $beforeRow, string $highestColumn, int $numberOfRows): void
    {
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
        for ($i = $beforeColumn; $i <= $highestColumnIndex; ++$i) {
            // Style
            $coordinate = Coordinate::stringFromColumnIndex($i) . ($beforeRow - 1);
            if ($worksheet->cellExists($coordinate)) {
                $xfIndex = $worksheet->getCell($coordinate)->getXfIndex();
                for ($j = $beforeRow; $j <= $beforeRow - 1 + $numberOfRows; ++$j) {
                    $worksheet->getCell(Coordinate::stringFromColumnIndex($i) . $j)->setXfIndex($xfIndex);
                }
            }
        }
    }

    /**
     * __clone implementation. Cloning should not be allowed in a Singleton!
     */
    final public function __clone()
    {
        throw new Exception('Cloning a Singleton is not allowed!');
    }
}
