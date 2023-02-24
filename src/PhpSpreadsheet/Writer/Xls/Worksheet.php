<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls;

use GdImage;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Shared\Xls;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\SheetView;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;

// Original file header of PEAR::Spreadsheet_Excel_Writer_Worksheet (used as the base for this class):
// -----------------------------------------------------------------------------------------
// /*
// *  Module written/ported by Xavier Noguer <xnoguer@rezebra.com>
// *
// *  The majority of this is _NOT_ my code.  I simply ported it from the
// *  PERL Spreadsheet::WriteExcel module.
// *
// *  The author of the Spreadsheet::WriteExcel module is John McNamara
// *  <jmcnamara@cpan.org>
// *
// *  I _DO_ maintain this code, and John McNamara has nothing to do with the
// *  porting of this code to PHP.  Any questions directly related to this
// *  class library should be directed to me.
// *
// *  License Information:
// *
// *    Spreadsheet_Excel_Writer:  A library for generating Excel Spreadsheets
// *    Copyright (c) 2002-2003 Xavier Noguer xnoguer@rezebra.com
// *
// *    This library is free software; you can redistribute it and/or
// *    modify it under the terms of the GNU Lesser General Public
// *    License as published by the Free Software Foundation; either
// *    version 2.1 of the License, or (at your option) any later version.
// *
// *    This library is distributed in the hope that it will be useful,
// *    but WITHOUT ANY WARRANTY; without even the implied warranty of
// *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// *    Lesser General Public License for more details.
// *
// *    You should have received a copy of the GNU Lesser General Public
// *    License along with this library; if not, write to the Free Software
// *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// */
class Worksheet extends BIFFwriter
{
    /** @var int */
    private static $always0 = 0;

    /** @var int */
    private static $always1 = 1;

    /**
     * Formula parser.
     *
     * @var \PhpOffice\PhpSpreadsheet\Writer\Xls\Parser
     */
    private $parser;

    /**
     * Array containing format information for columns.
     *
     * @var array
     */
    private $columnInfo;

    /**
     * The active pane for the worksheet.
     *
     * @var int
     */
    private $activePane;

    /**
     * Whether to use outline.
     *
     * @var bool
     */
    private $outlineOn;

    /**
     * Auto outline styles.
     *
     * @var bool
     */
    private $outlineStyle;

    /**
     * Whether to have outline summary below.
     * Not currently used.
     *
     * @var bool
     */
    private $outlineBelow; //* @phpstan-ignore-line

    /**
     * Whether to have outline summary at the right.
     * Not currently used.
     *
     * @var bool
     */
    private $outlineRight; //* @phpstan-ignore-line

    /**
     * Reference to the total number of strings in the workbook.
     *
     * @var int
     */
    private $stringTotal;

    /**
     * Reference to the number of unique strings in the workbook.
     *
     * @var int
     */
    private $stringUnique;

    /**
     * Reference to the array containing all the unique strings in the workbook.
     *
     * @var array
     */
    private $stringTable;

    /**
     * Color cache.
     *
     * @var array
     */
    private $colors;

    /**
     * Index of first used row (at least 0).
     *
     * @var int
     */
    private $firstRowIndex;

    /**
     * Index of last used row. (no used rows means -1).
     *
     * @var int
     */
    private $lastRowIndex;

    /**
     * Index of first used column (at least 0).
     *
     * @var int
     */
    private $firstColumnIndex;

    /**
     * Index of last used column (no used columns means -1).
     *
     * @var int
     */
    private $lastColumnIndex;

    /**
     * Sheet object.
     *
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public $phpSheet;

    /**
     * Escher object corresponding to MSODRAWING.
     *
     * @var null|\PhpOffice\PhpSpreadsheet\Shared\Escher
     */
    private $escher;

    /**
     * Array of font hashes associated to FONT records index.
     *
     * @var array
     */
    public $fontHashIndex;

    /**
     * @var bool
     */
    private $preCalculateFormulas;

    /**
     * @var int
     */
    private $printHeaders;

    /**
     * Constructor.
     *
     * @param int $str_total Total number of strings
     * @param int $str_unique Total number of unique strings
     * @param array $str_table String Table
     * @param array $colors Colour Table
     * @param Parser $parser The formula parser created for the Workbook
     * @param bool $preCalculateFormulas Flag indicating whether formulas should be calculated or just written
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $phpSheet The worksheet to write
     */
    public function __construct(&$str_total, &$str_unique, &$str_table, &$colors, Parser $parser, $preCalculateFormulas, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $phpSheet)
    {
        // It needs to call its parent's constructor explicitly
        parent::__construct();

        $this->preCalculateFormulas = $preCalculateFormulas;
        $this->stringTotal = &$str_total;
        $this->stringUnique = &$str_unique;
        $this->stringTable = &$str_table;
        $this->colors = &$colors;
        $this->parser = $parser;

        $this->phpSheet = $phpSheet;

        $this->columnInfo = [];
        $this->activePane = 3;

        $this->printHeaders = 0;

        $this->outlineStyle = false;
        $this->outlineBelow = true;
        $this->outlineRight = true;
        $this->outlineOn = true;

        $this->fontHashIndex = [];

        // calculate values for DIMENSIONS record
        $minR = 1;
        $minC = 'A';

        $maxR = $this->phpSheet->getHighestRow();
        $maxC = $this->phpSheet->getHighestColumn();

        // Determine lowest and highest column and row
        $this->firstRowIndex = $minR;
        $this->lastRowIndex = ($maxR > 65535) ? 65535 : $maxR;

        $this->firstColumnIndex = Coordinate::columnIndexFromString($minC);
        $this->lastColumnIndex = Coordinate::columnIndexFromString($maxC);

        if ($this->lastColumnIndex > 255) {
            $this->lastColumnIndex = 255;
        }
    }

    /**
     * Add data to the beginning of the workbook (note the reverse order)
     * and to the end of the workbook.
     *
     * @see \PhpOffice\PhpSpreadsheet\Writer\Xls\Workbook::storeWorkbook()
     */
    public function close(): void
    {
        $phpSheet = $this->phpSheet;

        // Storing selected cells and active sheet because it changes while parsing cells with formulas.
        $selectedCells = $this->phpSheet->getSelectedCells();
        $activeSheetIndex = $this->phpSheet->getParentOrThrow()->getActiveSheetIndex();

        // Write BOF record
        $this->storeBof(0x0010);

        // Write PRINTHEADERS
        $this->writePrintHeaders();

        // Write PRINTGRIDLINES
        $this->writePrintGridlines();

        // Write GRIDSET
        $this->writeGridset();

        // Calculate column widths
        $phpSheet->calculateColumnWidths();

        // Column dimensions
        if (($defaultWidth = $phpSheet->getDefaultColumnDimension()->getWidth()) < 0) {
            $defaultWidth = \PhpOffice\PhpSpreadsheet\Shared\Font::getDefaultColumnWidthByFont($phpSheet->getParentOrThrow()->getDefaultStyle()->getFont());
        }

        $columnDimensions = $phpSheet->getColumnDimensions();
        $maxCol = $this->lastColumnIndex - 1;
        for ($i = 0; $i <= $maxCol; ++$i) {
            $hidden = 0;
            $level = 0;
            $xfIndex = 15; // there are 15 cell style Xfs

            $width = $defaultWidth;

            $columnLetter = Coordinate::stringFromColumnIndex($i + 1);
            if (isset($columnDimensions[$columnLetter])) {
                $columnDimension = $columnDimensions[$columnLetter];
                if ($columnDimension->getWidth() >= 0) {
                    $width = $columnDimension->getWidth();
                }
                $hidden = $columnDimension->getVisible() ? 0 : 1;
                $level = $columnDimension->getOutlineLevel();
                $xfIndex = $columnDimension->getXfIndex() + 15; // there are 15 cell style Xfs
            }

            // Components of columnInfo:
            // $firstcol first column on the range
            // $lastcol  last column on the range
            // $width    width to set
            // $xfIndex  The optional cell style Xf index to apply to the columns
            // $hidden   The optional hidden atribute
            // $level    The optional outline level
            $this->columnInfo[] = [$i, $i, $width, $xfIndex, $hidden, $level];
        }

        // Write GUTS
        $this->writeGuts();

        // Write DEFAULTROWHEIGHT
        $this->writeDefaultRowHeight();
        // Write WSBOOL
        $this->writeWsbool();
        // Write horizontal and vertical page breaks
        $this->writeBreaks();
        // Write page header
        $this->writeHeader();
        // Write page footer
        $this->writeFooter();
        // Write page horizontal centering
        $this->writeHcenter();
        // Write page vertical centering
        $this->writeVcenter();
        // Write left margin
        $this->writeMarginLeft();
        // Write right margin
        $this->writeMarginRight();
        // Write top margin
        $this->writeMarginTop();
        // Write bottom margin
        $this->writeMarginBottom();
        // Write page setup
        $this->writeSetup();
        // Write sheet protection
        $this->writeProtect();
        // Write SCENPROTECT
        $this->writeScenProtect();
        // Write OBJECTPROTECT
        $this->writeObjectProtect();
        // Write sheet password
        $this->writePassword();
        // Write DEFCOLWIDTH record
        $this->writeDefcol();

        // Write the COLINFO records if they exist
        if (!empty($this->columnInfo)) {
            $colcount = count($this->columnInfo);
            for ($i = 0; $i < $colcount; ++$i) {
                $this->writeColinfo($this->columnInfo[$i]);
            }
        }
        $autoFilterRange = $phpSheet->getAutoFilter()->getRange();
        if (!empty($autoFilterRange)) {
            // Write AUTOFILTERINFO
            $this->writeAutoFilterInfo();
        }

        // Write sheet dimensions
        $this->writeDimensions();

        // Row dimensions
        foreach ($phpSheet->getRowDimensions() as $rowDimension) {
            $xfIndex = $rowDimension->getXfIndex() + 15; // there are 15 cellXfs
            $this->writeRow(
                $rowDimension->getRowIndex() - 1,
                (int) $rowDimension->getRowHeight(),
                $xfIndex,
                !$rowDimension->getVisible(),
                $rowDimension->getOutlineLevel()
            );
        }

        // Write Cells
        foreach ($phpSheet->getCellCollection()->getSortedCoordinates() as $coordinate) {
            /** @var Cell $cell */
            $cell = $phpSheet->getCellCollection()->get($coordinate);
            $row = $cell->getRow() - 1;
            $column = Coordinate::columnIndexFromString($cell->getColumn()) - 1;

            // Don't break Excel break the code!
            if ($row > 65535 || $column > 255) {
                throw new WriterException('Rows or columns overflow! Excel5 has limit to 65535 rows and 255 columns. Use XLSX instead.');
            }

            // Write cell value
            $xfIndex = $cell->getXfIndex() + 15; // there are 15 cell style Xfs

            $cVal = $cell->getValue();
            if ($cVal instanceof RichText) {
                $arrcRun = [];
                $str_pos = 0;
                $elements = $cVal->getRichTextElements();
                foreach ($elements as $element) {
                    // FONT Index
                    $str_fontidx = 0;
                    if ($element instanceof Run) {
                        $getFont = $element->getFont();
                        if ($getFont !== null) {
                            $str_fontidx = $this->fontHashIndex[$getFont->getHashCode()];
                        }
                    }
                    $arrcRun[] = ['strlen' => $str_pos, 'fontidx' => $str_fontidx];
                    // Position FROM
                    $str_pos += StringHelper::countCharacters($element->getText(), 'UTF-8');
                }
                $this->writeRichTextString($row, $column, $cVal->getPlainText(), $xfIndex, $arrcRun);
            } else {
                switch ($cell->getDatatype()) {
                    case DataType::TYPE_STRING:
                    case DataType::TYPE_INLINE:
                    case DataType::TYPE_NULL:
                        if ($cVal === '' || $cVal === null) {
                            $this->writeBlank($row, $column, $xfIndex);
                        } else {
                            $this->writeString($row, $column, $cVal, $xfIndex);
                        }

                        break;
                    case DataType::TYPE_NUMERIC:
                        $this->writeNumber($row, $column, $cVal, $xfIndex);

                        break;
                    case DataType::TYPE_FORMULA:
                        $calculatedValue = $this->preCalculateFormulas ?
                            $cell->getCalculatedValue() : null;
                        if (self::WRITE_FORMULA_EXCEPTION == $this->writeFormula($row, $column, $cVal, $xfIndex, $calculatedValue)) {
                            if ($calculatedValue === null) {
                                $calculatedValue = $cell->getCalculatedValue();
                            }
                            $calctype = gettype($calculatedValue);
                            switch ($calctype) {
                                case 'integer':
                                case 'double':
                                    $this->writeNumber($row, $column, (float) $calculatedValue, $xfIndex);

                                    break;
                                case 'string':
                                    $this->writeString($row, $column, $calculatedValue, $xfIndex);

                                    break;
                                case 'boolean':
                                    $this->writeBoolErr($row, $column, (int) $calculatedValue, 0, $xfIndex);

                                    break;
                                default:
                                    $this->writeString($row, $column, $cVal, $xfIndex);
                            }
                        }

                        break;
                    case DataType::TYPE_BOOL:
                        $this->writeBoolErr($row, $column, $cVal, 0, $xfIndex);

                        break;
                    case DataType::TYPE_ERROR:
                        $this->writeBoolErr($row, $column, ErrorCode::error($cVal), 1, $xfIndex);

                        break;
                }
            }
        }

        // Append
        $this->writeMsoDrawing();

        // Restoring active sheet.
        $this->phpSheet->getParentOrThrow()->setActiveSheetIndex($activeSheetIndex);

        // Write WINDOW2 record
        $this->writeWindow2();

        // Write PLV record
        $this->writePageLayoutView();

        // Write ZOOM record
        $this->writeZoom();
        if ($phpSheet->getFreezePane()) {
            $this->writePanes();
        }

        // Restoring selected cells.
        $this->phpSheet->setSelectedCells($selectedCells);

        // Write SELECTION record
        $this->writeSelection();

        // Write MergedCellsTable Record
        $this->writeMergedCells();

        // Hyperlinks
        foreach ($phpSheet->getHyperLinkCollection() as $coordinate => $hyperlink) {
            [$column, $row] = Coordinate::indexesFromString($coordinate);

            $url = $hyperlink->getUrl();

            if (strpos($url, 'sheet://') !== false) {
                // internal to current workbook
                $url = str_replace('sheet://', 'internal:', $url);
            } elseif (preg_match('/^(http:|https:|ftp:|mailto:)/', $url)) {
                // URL
            } else {
                // external (local file)
                $url = 'external:' . $url;
            }

            $this->writeUrl($row - 1, $column - 1, $url);
        }

        $this->writeDataValidity();
        $this->writeSheetLayout();

        // Write SHEETPROTECTION record
        $this->writeSheetProtection();
        $this->writeRangeProtection();

        // Write Conditional Formatting Rules and Styles
        $this->writeConditionalFormatting();

        $this->storeEof();
    }

    private function writeConditionalFormatting(): void
    {
        $conditionalFormulaHelper = new ConditionalHelper($this->parser);

        $arrConditionalStyles = $this->phpSheet->getConditionalStylesCollection();
        if (!empty($arrConditionalStyles)) {
            $arrConditional = [];

            // Write ConditionalFormattingTable records
            foreach ($arrConditionalStyles as $cellCoordinate => $conditionalStyles) {
                $cfHeaderWritten = false;
                foreach ($conditionalStyles as $conditional) {
                    /** @var Conditional $conditional */
                    if (
                        $conditional->getConditionType() === Conditional::CONDITION_EXPRESSION ||
                        $conditional->getConditionType() === Conditional::CONDITION_CELLIS
                    ) {
                        // Write CFHEADER record (only if there are Conditional Styles that we are able to write)
                        if ($cfHeaderWritten === false) {
                            $cfHeaderWritten = $this->writeCFHeader($cellCoordinate, $conditionalStyles);
                        }
                        if ($cfHeaderWritten === true && !isset($arrConditional[$conditional->getHashCode()])) {
                            // This hash code has been handled
                            $arrConditional[$conditional->getHashCode()] = true;

                            // Write CFRULE record
                            $this->writeCFRule($conditionalFormulaHelper, $conditional, $cellCoordinate);
                        }
                    }
                }
            }
        }
    }

    /**
     * Write a cell range address in BIFF8
     * always fixed range
     * See section 2.5.14 in OpenOffice.org's Documentation of the Microsoft Excel File Format.
     *
     * @param string $range E.g. 'A1' or 'A1:B6'
     *
     * @return string Binary data
     */
    private function writeBIFF8CellRangeAddressFixed($range)
    {
        $explodes = explode(':', $range);

        // extract first cell, e.g. 'A1'
        $firstCell = $explodes[0];

        // extract last cell, e.g. 'B6'
        if (count($explodes) == 1) {
            $lastCell = $firstCell;
        } else {
            $lastCell = $explodes[1];
        }

        $firstCellCoordinates = Coordinate::indexesFromString($firstCell); // e.g. [0, 1]
        $lastCellCoordinates = Coordinate::indexesFromString($lastCell); // e.g. [1, 6]

        return pack('vvvv', $firstCellCoordinates[1] - 1, $lastCellCoordinates[1] - 1, $firstCellCoordinates[0] - 1, $lastCellCoordinates[0] - 1);
    }

    /**
     * Retrieves data from memory in one chunk, or from disk
     * sized chunks.
     *
     * @return string The data
     */
    public function getData()
    {
        // Return data stored in memory
        if (isset($this->_data)) {
            $tmp = $this->_data;
            $this->_data = null;

            return $tmp;
        }

        // No data to return
        return '';
    }

    /**
     * Set the option to print the row and column headers on the printed page.
     *
     * @param int $print Whether to print the headers or not. Defaults to 1 (print).
     */
    public function printRowColHeaders($print = 1): void
    {
        $this->printHeaders = $print;
    }

    /**
     * This method sets the properties for outlining and grouping. The defaults
     * correspond to Excel's defaults.
     *
     * @param bool $visible
     * @param bool $symbols_below
     * @param bool $symbols_right
     * @param bool $auto_style
     */
    public function setOutline($visible = true, $symbols_below = true, $symbols_right = true, $auto_style = false): void
    {
        $this->outlineOn = $visible;
        $this->outlineBelow = $symbols_below;
        $this->outlineRight = $symbols_right;
        $this->outlineStyle = $auto_style;
    }

    /**
     * Write a double to the specified row and column (zero indexed).
     * An integer can be written as a double. Excel will display an
     * integer. $format is optional.
     *
     * Returns  0 : normal termination
     *         -2 : row or column out of range
     *
     * @param int $row Zero indexed row
     * @param int $col Zero indexed column
     * @param float $num The number to write
     * @param mixed $xfIndex The optional XF format
     *
     * @return int
     */
    private function writeNumber($row, $col, $num, $xfIndex)
    {
        $record = 0x0203; // Record identifier
        $length = 0x000E; // Number of bytes to follow

        $header = pack('vv', $record, $length);
        $data = pack('vvv', $row, $col, $xfIndex);
        $xl_double = pack('d', $num);
        if (self::getByteOrder()) { // if it's Big Endian
            $xl_double = strrev($xl_double);
        }

        $this->append($header . $data . $xl_double);

        return 0;
    }

    /**
     * Write a LABELSST record or a LABEL record. Which one depends on BIFF version.
     *
     * @param int $row Row index (0-based)
     * @param int $col Column index (0-based)
     * @param string $str The string
     * @param int $xfIndex Index to XF record
     */
    private function writeString($row, $col, $str, $xfIndex): void
    {
        $this->writeLabelSst($row, $col, $str, $xfIndex);
    }

    /**
     * Write a LABELSST record or a LABEL record. Which one depends on BIFF version
     * It differs from writeString by the writing of rich text strings.
     *
     * @param int $row Row index (0-based)
     * @param int $col Column index (0-based)
     * @param string $str The string
     * @param int $xfIndex The XF format index for the cell
     * @param array $arrcRun Index to Font record and characters beginning
     */
    private function writeRichTextString($row, $col, $str, $xfIndex, $arrcRun): void
    {
        $record = 0x00FD; // Record identifier
        $length = 0x000A; // Bytes to follow
        $str = StringHelper::UTF8toBIFF8UnicodeShort($str, $arrcRun);

        // check if string is already present
        if (!isset($this->stringTable[$str])) {
            $this->stringTable[$str] = $this->stringUnique++;
        }
        ++$this->stringTotal;

        $header = pack('vv', $record, $length);
        $data = pack('vvvV', $row, $col, $xfIndex, $this->stringTable[$str]);
        $this->append($header . $data);
    }

    /**
     * Write a string to the specified row and column (zero indexed).
     * This is the BIFF8 version (no 255 chars limit).
     * $format is optional.
     *
     * @param int $row Zero indexed row
     * @param int $col Zero indexed column
     * @param string $str The string to write
     * @param mixed $xfIndex The XF format index for the cell
     */
    private function writeLabelSst($row, $col, $str, $xfIndex): void
    {
        $record = 0x00FD; // Record identifier
        $length = 0x000A; // Bytes to follow

        $str = StringHelper::UTF8toBIFF8UnicodeLong($str);

        // check if string is already present
        if (!isset($this->stringTable[$str])) {
            $this->stringTable[$str] = $this->stringUnique++;
        }
        ++$this->stringTotal;

        $header = pack('vv', $record, $length);
        $data = pack('vvvV', $row, $col, $xfIndex, $this->stringTable[$str]);
        $this->append($header . $data);
    }

    /**
     * Write a blank cell to the specified row and column (zero indexed).
     * A blank cell is used to specify formatting without adding a string
     * or a number.
     *
     * A blank cell without a format serves no purpose. Therefore, we don't write
     * a BLANK record unless a format is specified.
     *
     * Returns  0 : normal termination (including no format)
     *         -1 : insufficient number of arguments
     *         -2 : row or column out of range
     *
     * @param int $row Zero indexed row
     * @param int $col Zero indexed column
     * @param mixed $xfIndex The XF format index
     *
     * @return int
     */
    public function writeBlank($row, $col, $xfIndex)
    {
        $record = 0x0201; // Record identifier
        $length = 0x0006; // Number of bytes to follow

        $header = pack('vv', $record, $length);
        $data = pack('vvv', $row, $col, $xfIndex);
        $this->append($header . $data);

        return 0;
    }

    /**
     * Write a boolean or an error type to the specified row and column (zero indexed).
     *
     * @param int $row Row index (0-based)
     * @param int $col Column index (0-based)
     * @param int $value
     * @param int $isError Error or Boolean?
     * @param int $xfIndex
     *
     * @return int
     */
    private function writeBoolErr($row, $col, $value, $isError, $xfIndex)
    {
        $record = 0x0205;
        $length = 8;

        $header = pack('vv', $record, $length);
        $data = pack('vvvCC', $row, $col, $xfIndex, $value, $isError);
        $this->append($header . $data);

        return 0;
    }

    const WRITE_FORMULA_NORMAL = 0;
    const WRITE_FORMULA_ERRORS = -1;
    const WRITE_FORMULA_RANGE = -2;
    const WRITE_FORMULA_EXCEPTION = -3;

    /** @var bool */
    private static $allowThrow = false;

    public static function setAllowThrow(bool $allowThrow): void
    {
        self::$allowThrow = $allowThrow;
    }

    public static function getAllowThrow(): bool
    {
        return self::$allowThrow;
    }

    /**
     * Write a formula to the specified row and column (zero indexed).
     * The textual representation of the formula is passed to the parser in
     * Parser.php which returns a packed binary string.
     *
     * Returns  0 : WRITE_FORMULA_NORMAL  normal termination
     *         -1 : WRITE_FORMULA_ERRORS formula errors (bad formula)
     *         -2 : WRITE_FORMULA_RANGE  row or column out of range
     *         -3 : WRITE_FORMULA_EXCEPTION parse raised exception, probably due to definedname
     *
     * @param int $row Zero indexed row
     * @param int $col Zero indexed column
     * @param string $formula The formula text string
     * @param mixed $xfIndex The XF format index
     * @param mixed $calculatedValue Calculated value
     *
     * @return int
     */
    private function writeFormula($row, $col, $formula, $xfIndex, $calculatedValue)
    {
        $record = 0x0006; // Record identifier
        // Initialize possible additional value for STRING record that should be written after the FORMULA record?
        $stringValue = null;

        // calculated value
        if (isset($calculatedValue)) {
            // Since we can't yet get the data type of the calculated value,
            // we use best effort to determine data type
            if (is_bool($calculatedValue)) {
                // Boolean value
                $num = pack('CCCvCv', 0x01, 0x00, (int) $calculatedValue, 0x00, 0x00, 0xFFFF);
            } elseif (is_int($calculatedValue) || is_float($calculatedValue)) {
                // Numeric value
                $num = pack('d', $calculatedValue);
            } elseif (is_string($calculatedValue)) {
                $errorCodes = DataType::getErrorCodes();
                if (isset($errorCodes[$calculatedValue])) {
                    // Error value
                    $num = pack('CCCvCv', 0x02, 0x00, ErrorCode::error($calculatedValue), 0x00, 0x00, 0xFFFF);
                } elseif ($calculatedValue === '') {
                    // Empty string (and BIFF8)
                    $num = pack('CCCvCv', 0x03, 0x00, 0x00, 0x00, 0x00, 0xFFFF);
                } else {
                    // Non-empty string value (or empty string BIFF5)
                    $stringValue = $calculatedValue;
                    $num = pack('CCCvCv', 0x00, 0x00, 0x00, 0x00, 0x00, 0xFFFF);
                }
            } else {
                // We are really not supposed to reach here
                $num = pack('d', 0x00);
            }
        } else {
            $num = pack('d', 0x00);
        }

        $grbit = 0x03; // Option flags
        $unknown = 0x0000; // Must be zero

        // Strip the '=' or '@' sign at the beginning of the formula string
        if ($formula[0] == '=') {
            $formula = substr($formula, 1);
        } else {
            // Error handling
            $this->writeString($row, $col, 'Unrecognised character for formula', 0);

            return self::WRITE_FORMULA_ERRORS;
        }

        // Parse the formula using the parser in Parser.php
        try {
            $this->parser->parse($formula);
            $formula = $this->parser->toReversePolish();

            $formlen = strlen($formula); // Length of the binary string
            $length = 0x16 + $formlen; // Length of the record data

            $header = pack('vv', $record, $length);

            $data = pack('vvv', $row, $col, $xfIndex)
                . $num
                . pack('vVv', $grbit, $unknown, $formlen);
            $this->append($header . $data . $formula);

            // Append also a STRING record if necessary
            if ($stringValue !== null) {
                $this->writeStringRecord($stringValue);
            }

            return self::WRITE_FORMULA_NORMAL;
        } catch (PhpSpreadsheetException $e) {
            if (self::$allowThrow) {
                throw $e;
            }

            return self::WRITE_FORMULA_EXCEPTION;
        }
    }

    /**
     * Write a STRING record. This.
     *
     * @param string $stringValue
     */
    private function writeStringRecord($stringValue): void
    {
        $record = 0x0207; // Record identifier
        $data = StringHelper::UTF8toBIFF8UnicodeLong($stringValue);

        $length = strlen($data);
        $header = pack('vv', $record, $length);

        $this->append($header . $data);
    }

    /**
     * Write a hyperlink.
     * This is comprised of two elements: the visible label and
     * the invisible link. The visible label is the same as the link unless an
     * alternative string is specified. The label is written using the
     * writeString() method. Therefore the 255 characters string limit applies.
     * $string and $format are optional.
     *
     * The hyperlink can be to a http, ftp, mail, internal sheet (not yet), or external
     * directory url.
     *
     * @param int $row Row
     * @param int $col Column
     * @param string $url URL string
     */
    private function writeUrl($row, $col, $url): void
    {
        // Add start row and col to arg list
        $this->writeUrlRange($row, $col, $row, $col, $url);
    }

    /**
     * This is the more general form of writeUrl(). It allows a hyperlink to be
     * written to a range of cells. This function also decides the type of hyperlink
     * to be written. These are either, Web (http, ftp, mailto), Internal
     * (Sheet1!A1) or external ('c:\temp\foo.xls#Sheet1!A1').
     *
     * @param int $row1 Start row
     * @param int $col1 Start column
     * @param int $row2 End row
     * @param int $col2 End column
     * @param string $url URL string
     *
     * @see writeUrl()
     */
    private function writeUrlRange($row1, $col1, $row2, $col2, $url): void
    {
        // Check for internal/external sheet links or default to web link
        if (preg_match('[^internal:]', $url)) {
            $this->writeUrlInternal($row1, $col1, $row2, $col2, $url);
        }
        if (preg_match('[^external:]', $url)) {
            $this->writeUrlExternal($row1, $col1, $row2, $col2, $url);
        }

        $this->writeUrlWeb($row1, $col1, $row2, $col2, $url);
    }

    /**
     * Used to write http, ftp and mailto hyperlinks.
     * The link type ($options) is 0x03 is the same as absolute dir ref without
     * sheet. However it is differentiated by the $unknown2 data stream.
     *
     * @param int $row1 Start row
     * @param int $col1 Start column
     * @param int $row2 End row
     * @param int $col2 End column
     * @param string $url URL string
     *
     * @see writeUrl()
     */
    public function writeUrlWeb($row1, $col1, $row2, $col2, $url): void
    {
        $record = 0x01B8; // Record identifier

        // Pack the undocumented parts of the hyperlink stream
        $unknown1 = pack('H*', 'D0C9EA79F9BACE118C8200AA004BA90B02000000');
        $unknown2 = pack('H*', 'E0C9EA79F9BACE118C8200AA004BA90B');

        // Pack the option flags
        $options = pack('V', 0x03);

        // Convert URL to a null terminated wchar string

        /** @phpstan-ignore-next-line */
        $url = implode("\0", preg_split("''", $url, -1, PREG_SPLIT_NO_EMPTY));
        $url = $url . "\0\0\0";

        // Pack the length of the URL
        $url_len = pack('V', strlen($url));

        // Calculate the data length
        $length = 0x34 + strlen($url);

        // Pack the header data
        $header = pack('vv', $record, $length);
        $data = pack('vvvv', $row1, $row2, $col1, $col2);

        // Write the packed data
        $this->append($header . $data . $unknown1 . $options . $unknown2 . $url_len . $url);
    }

    /**
     * Used to write internal reference hyperlinks such as "Sheet1!A1".
     *
     * @param int $row1 Start row
     * @param int $col1 Start column
     * @param int $row2 End row
     * @param int $col2 End column
     * @param string $url URL string
     *
     * @see writeUrl()
     */
    private function writeUrlInternal($row1, $col1, $row2, $col2, $url): void
    {
        $record = 0x01B8; // Record identifier

        // Strip URL type
        $url = (string) preg_replace('/^internal:/', '', $url);

        // Pack the undocumented parts of the hyperlink stream
        $unknown1 = pack('H*', 'D0C9EA79F9BACE118C8200AA004BA90B02000000');

        // Pack the option flags
        $options = pack('V', 0x08);

        // Convert the URL type and to a null terminated wchar string
        $url .= "\0";

        // character count
        $url_len = StringHelper::countCharacters($url);
        $url_len = pack('V', $url_len);

        $url = StringHelper::convertEncoding($url, 'UTF-16LE', 'UTF-8');

        // Calculate the data length
        $length = 0x24 + strlen($url);

        // Pack the header data
        $header = pack('vv', $record, $length);
        $data = pack('vvvv', $row1, $row2, $col1, $col2);

        // Write the packed data
        $this->append($header . $data . $unknown1 . $options . $url_len . $url);
    }

    /**
     * Write links to external directory names such as 'c:\foo.xls',
     * c:\foo.xls#Sheet1!A1', '../../foo.xls'. and '../../foo.xls#Sheet1!A1'.
     *
     * Note: Excel writes some relative links with the $dir_long string. We ignore
     * these cases for the sake of simpler code.
     *
     * @param int $row1 Start row
     * @param int $col1 Start column
     * @param int $row2 End row
     * @param int $col2 End column
     * @param string $url URL string
     *
     * @see writeUrl()
     */
    private function writeUrlExternal($row1, $col1, $row2, $col2, $url): void
    {
        // Network drives are different. We will handle them separately
        // MS/Novell network drives and shares start with \\
        if (preg_match('[^external:\\\\]', $url)) {
            return;
        }

        $record = 0x01B8; // Record identifier

        // Strip URL type and change Unix dir separator to Dos style (if needed)
        //
        $url = (string) preg_replace(['/^external:/', '/\//'], ['', '\\'], $url);

        // Determine if the link is relative or absolute:
        //   relative if link contains no dir separator, "somefile.xls"
        //   relative if link starts with up-dir, "..\..\somefile.xls"
        //   otherwise, absolute

        $absolute = 0x00; // relative path
        if (preg_match('/^[A-Z]:/', $url)) {
            $absolute = 0x02; // absolute path on Windows, e.g. C:\...
        }
        $link_type = 0x01 | $absolute;

        // Determine if the link contains a sheet reference and change some of the
        // parameters accordingly.
        // Split the dir name and sheet name (if it exists)
        $dir_long = $url;
        if (preg_match('/\\#/', $url)) {
            $link_type |= 0x08;
        }

        // Pack the link type
        $link_type = pack('V', $link_type);

        // Calculate the up-level dir count e.g.. (..\..\..\ == 3)
        $up_count = preg_match_all('/\\.\\.\\\\/', $dir_long, $useless);
        $up_count = pack('v', $up_count);

        // Store the short dos dir name (null terminated)
        $dir_short = (string) preg_replace('/\\.\\.\\\\/', '', $dir_long) . "\0";

        // Store the long dir name as a wchar string (non-null terminated)
        //$dir_long = $dir_long . "\0";

        // Pack the lengths of the dir strings
        $dir_short_len = pack('V', strlen($dir_short));
        //$dir_long_len = pack('V', strlen($dir_long));
        $stream_len = pack('V', 0); //strlen($dir_long) + 0x06);

        // Pack the undocumented parts of the hyperlink stream
        $unknown1 = pack('H*', 'D0C9EA79F9BACE118C8200AA004BA90B02000000');
        $unknown2 = pack('H*', '0303000000000000C000000000000046');
        $unknown3 = pack('H*', 'FFFFADDE000000000000000000000000000000000000000');
        //$unknown4 = pack('v', 0x03);

        // Pack the main data stream
        $data = pack('vvvv', $row1, $row2, $col1, $col2) .
            $unknown1 .
            $link_type .
            $unknown2 .
            $up_count .
            $dir_short_len .
            $dir_short .
            $unknown3 .
            $stream_len; /*.
                          $dir_long_len .
                          $unknown4     .
                          $dir_long     .
                          $sheet_len    .
                          $sheet        ;*/

        // Pack the header data
        $length = strlen($data);
        $header = pack('vv', $record, $length);

        // Write the packed data
        $this->append($header . $data);
    }

    /**
     * This method is used to set the height and format for a row.
     *
     * @param int $row The row to set
     * @param int $height Height we are giving to the row.
     *                        Use null to set XF without setting height
     * @param int $xfIndex The optional cell style Xf index to apply to the columns
     * @param bool $hidden The optional hidden attribute
     * @param int $level The optional outline level for row, in range [0,7]
     */
    private function writeRow($row, $height, $xfIndex, $hidden = false, $level = 0): void
    {
        $record = 0x0208; // Record identifier
        $length = 0x0010; // Number of bytes to follow

        $colMic = 0x0000; // First defined column
        $colMac = 0x0000; // Last defined column
        $irwMac = 0x0000; // Used by Excel to optimise loading
        $reserved = 0x0000; // Reserved
        $grbit = 0x0000; // Option flags
        $ixfe = $xfIndex;

        if ($height < 0) {
            $height = null;
        }

        // Use writeRow($row, null, $XF) to set XF format without setting height
        if ($height !== null) {
            $miyRw = $height * 20; // row height
        } else {
            $miyRw = 0xff; // default row height is 256
        }

        // Set the options flags. fUnsynced is used to show that the font and row
        // heights are not compatible. This is usually the case for WriteExcel.
        // The collapsed flag 0x10 doesn't seem to be used to indicate that a row
        // is collapsed. Instead it is used to indicate that the previous row is
        // collapsed. The zero height flag, 0x20, is used to collapse a row.

        $grbit |= $level;
        if ($hidden === true) {
            $grbit |= 0x0030;
        }
        if ($height !== null) {
            $grbit |= 0x0040; // fUnsynced
        }
        if ($xfIndex !== 0xF) {
            $grbit |= 0x0080;
        }
        $grbit |= 0x0100;

        $header = pack('vv', $record, $length);
        $data = pack('vvvvvvvv', $row, $colMic, $colMac, $miyRw, $irwMac, $reserved, $grbit, $ixfe);
        $this->append($header . $data);
    }

    /**
     * Writes Excel DIMENSIONS to define the area in which there is data.
     */
    private function writeDimensions(): void
    {
        $record = 0x0200; // Record identifier

        $length = 0x000E;
        $data = pack('VVvvv', $this->firstRowIndex, $this->lastRowIndex + 1, $this->firstColumnIndex, $this->lastColumnIndex + 1, 0x0000); // reserved

        $header = pack('vv', $record, $length);
        $this->append($header . $data);
    }

    /**
     * Write BIFF record Window2.
     */
    private function writeWindow2(): void
    {
        $record = 0x023E; // Record identifier
        $length = 0x0012;

        $rwTop = 0x0000; // Top row visible in window
        $colLeft = 0x0000; // Leftmost column visible in window

        // The options flags that comprise $grbit
        $fDspFmla = 0; // 0 - bit
        $fDspGrid = $this->phpSheet->getShowGridlines() ? 1 : 0; // 1
        $fDspRwCol = $this->phpSheet->getShowRowColHeaders() ? 1 : 0; // 2
        $fFrozen = $this->phpSheet->getFreezePane() ? 1 : 0; // 3
        $fDspZeros = 1; // 4
        $fDefaultHdr = 1; // 5
        $fArabic = $this->phpSheet->getRightToLeft() ? 1 : 0; // 6
        $fDspGuts = $this->outlineOn; // 7
        $fFrozenNoSplit = 0; // 0 - bit
        // no support in PhpSpreadsheet for selected sheet, therefore sheet is only selected if it is the active sheet
        $fSelected = ($this->phpSheet === $this->phpSheet->getParentOrThrow()->getActiveSheet()) ? 1 : 0;
        $fPageBreakPreview = $this->phpSheet->getSheetView()->getView() === SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW;

        $grbit = $fDspFmla;
        $grbit |= $fDspGrid << 1;
        $grbit |= $fDspRwCol << 2;
        $grbit |= $fFrozen << 3;
        $grbit |= $fDspZeros << 4;
        $grbit |= $fDefaultHdr << 5;
        $grbit |= $fArabic << 6;
        $grbit |= $fDspGuts << 7;
        $grbit |= $fFrozenNoSplit << 8;
        $grbit |= $fSelected << 9; // Selected sheets.
        $grbit |= $fSelected << 10; // Active sheet.
        $grbit |= $fPageBreakPreview << 11;

        $header = pack('vv', $record, $length);
        $data = pack('vvv', $grbit, $rwTop, $colLeft);

        // FIXME !!!
        $rgbHdr = 0x0040; // Row/column heading and gridline color index
        $zoom_factor_page_break = ($fPageBreakPreview ? $this->phpSheet->getSheetView()->getZoomScale() : 0x0000);
        $zoom_factor_normal = $this->phpSheet->getSheetView()->getZoomScaleNormal();

        $data .= pack('vvvvV', $rgbHdr, 0x0000, $zoom_factor_page_break, $zoom_factor_normal, 0x00000000);

        $this->append($header . $data);
    }

    /**
     * Write BIFF record DEFAULTROWHEIGHT.
     */
    private function writeDefaultRowHeight(): void
    {
        $defaultRowHeight = $this->phpSheet->getDefaultRowDimension()->getRowHeight();

        if ($defaultRowHeight < 0) {
            return;
        }

        // convert to twips
        $defaultRowHeight = (int) 20 * $defaultRowHeight;

        $record = 0x0225; // Record identifier
        $length = 0x0004; // Number of bytes to follow

        $header = pack('vv', $record, $length);
        $data = pack('vv', 1, $defaultRowHeight);
        $this->append($header . $data);
    }

    /**
     * Write BIFF record DEFCOLWIDTH if COLINFO records are in use.
     */
    private function writeDefcol(): void
    {
        $defaultColWidth = 8;

        $record = 0x0055; // Record identifier
        $length = 0x0002; // Number of bytes to follow

        $header = pack('vv', $record, $length);
        $data = pack('v', $defaultColWidth);
        $this->append($header . $data);
    }

    /**
     * Write BIFF record COLINFO to define column widths.
     *
     * Note: The SDK says the record length is 0x0B but Excel writes a 0x0C
     * length record.
     *
     * @param array $col_array This is the only parameter received and is composed of the following:
     *                0 => First formatted column,
     *                1 => Last formatted column,
     *                2 => Col width (8.43 is Excel default),
     *                3 => The optional XF format of the column,
     *                4 => Option flags.
     *                5 => Optional outline level
     */
    private function writeColinfo($col_array): void
    {
        $colFirst = $col_array[0] ?? null;
        $colLast = $col_array[1] ?? null;
        $coldx = $col_array[2] ?? 8.43;
        $xfIndex = $col_array[3] ?? 15;
        $grbit = $col_array[4] ?? 0;
        $level = $col_array[5] ?? 0;

        $record = 0x007D; // Record identifier
        $length = 0x000C; // Number of bytes to follow

        $coldx *= 256; // Convert to units of 1/256 of a char

        $ixfe = $xfIndex;
        $reserved = 0x0000; // Reserved

        $level = max(0, min($level, 7));
        $grbit |= $level << 8;

        $header = pack('vv', $record, $length);
        $data = pack('vvvvvv', $colFirst, $colLast, $coldx, $ixfe, $grbit, $reserved);
        $this->append($header . $data);
    }

    /**
     * Write BIFF record SELECTION.
     */
    private function writeSelection(): void
    {
        // look up the selected cell range
        $selectedCells = Coordinate::splitRange($this->phpSheet->getSelectedCells());
        $selectedCells = $selectedCells[0];
        if (count($selectedCells) == 2) {
            [$first, $last] = $selectedCells;
        } else {
            $first = $selectedCells[0];
            $last = $selectedCells[0];
        }

        [$colFirst, $rwFirst] = Coordinate::coordinateFromString($first);
        $colFirst = Coordinate::columnIndexFromString($colFirst) - 1; // base 0 column index
        --$rwFirst; // base 0 row index

        [$colLast, $rwLast] = Coordinate::coordinateFromString($last);
        $colLast = Coordinate::columnIndexFromString($colLast) - 1; // base 0 column index
        --$rwLast; // base 0 row index

        // make sure we are not out of bounds
        $colFirst = min($colFirst, 255);
        $colLast = min($colLast, 255);

        $rwFirst = min($rwFirst, 65535);
        $rwLast = min($rwLast, 65535);

        $record = 0x001D; // Record identifier
        $length = 0x000F; // Number of bytes to follow

        $pnn = $this->activePane; // Pane position
        $rwAct = $rwFirst; // Active row
        $colAct = $colFirst; // Active column
        $irefAct = 0; // Active cell ref
        $cref = 1; // Number of refs

        // Swap last row/col for first row/col as necessary
        if ($rwFirst > $rwLast) {
            [$rwFirst, $rwLast] = [$rwLast, $rwFirst];
        }

        if ($colFirst > $colLast) {
            [$colFirst, $colLast] = [$colLast, $colFirst];
        }

        $header = pack('vv', $record, $length);
        $data = pack('CvvvvvvCC', $pnn, $rwAct, $colAct, $irefAct, $cref, $rwFirst, $rwLast, $colFirst, $colLast);
        $this->append($header . $data);
    }

    /**
     * Store the MERGEDCELLS records for all ranges of merged cells.
     */
    private function writeMergedCells(): void
    {
        $mergeCells = $this->phpSheet->getMergeCells();
        $countMergeCells = count($mergeCells);

        if ($countMergeCells == 0) {
            return;
        }

        // maximum allowed number of merged cells per record
        $maxCountMergeCellsPerRecord = 1027;

        // record identifier
        $record = 0x00E5;

        // counter for total number of merged cells treated so far by the writer
        $i = 0;

        // counter for number of merged cells written in record currently being written
        $j = 0;

        // initialize record data
        $recordData = '';

        // loop through the merged cells
        foreach ($mergeCells as $mergeCell) {
            ++$i;
            ++$j;

            // extract the row and column indexes
            $range = Coordinate::splitRange($mergeCell);
            [$first, $last] = $range[0];
            [$firstColumn, $firstRow] = Coordinate::indexesFromString($first);
            [$lastColumn, $lastRow] = Coordinate::indexesFromString($last);

            $recordData .= pack('vvvv', $firstRow - 1, $lastRow - 1, $firstColumn - 1, $lastColumn - 1);

            // flush record if we have reached limit for number of merged cells, or reached final merged cell
            if ($j == $maxCountMergeCellsPerRecord || $i == $countMergeCells) {
                $recordData = pack('v', $j) . $recordData;
                $length = strlen($recordData);
                $header = pack('vv', $record, $length);
                $this->append($header . $recordData);

                // initialize for next record, if any
                $recordData = '';
                $j = 0;
            }
        }
    }

    /**
     * Write SHEETLAYOUT record.
     */
    private function writeSheetLayout(): void
    {
        if (!$this->phpSheet->isTabColorSet()) {
            return;
        }

        $recordData = pack(
            'vvVVVvv',
            0x0862,
            0x0000, // unused
            0x00000000, // unused
            0x00000000, // unused
            0x00000014, // size of record data
            $this->colors[$this->phpSheet->getTabColor()->getRGB()], // color index
            0x0000        // unused
        );

        $length = strlen($recordData);

        $record = 0x0862; // Record identifier
        $header = pack('vv', $record, $length);
        $this->append($header . $recordData);
    }

    private static function protectionBitsDefaultFalse(?bool $value, int $shift): int
    {
        if ($value === false) {
            return 1 << $shift;
        }

        return 0;
    }

    private static function protectionBitsDefaultTrue(?bool $value, int $shift): int
    {
        if ($value !== false) {
            return 1 << $shift;
        }

        return 0;
    }

    /**
     * Write SHEETPROTECTION.
     */
    private function writeSheetProtection(): void
    {
        // record identifier
        $record = 0x0867;

        // prepare options
        $protection = $this->phpSheet->getProtection();
        $options = self::protectionBitsDefaultTrue($protection->getObjects(), 0)
            | self::protectionBitsDefaultTrue($protection->getScenarios(), 1)
            | self::protectionBitsDefaultFalse($protection->getFormatCells(), 2)
            | self::protectionBitsDefaultFalse($protection->getFormatColumns(), 3)
            | self::protectionBitsDefaultFalse($protection->getFormatRows(), 4)
            | self::protectionBitsDefaultFalse($protection->getInsertColumns(), 5)
            | self::protectionBitsDefaultFalse($protection->getInsertRows(), 6)
            | self::protectionBitsDefaultFalse($protection->getInsertHyperlinks(), 7)
            | self::protectionBitsDefaultFalse($protection->getDeleteColumns(), 8)
            | self::protectionBitsDefaultFalse($protection->getDeleteRows(), 9)
            | self::protectionBitsDefaultTrue($protection->getSelectLockedCells(), 10)
            | self::protectionBitsDefaultFalse($protection->getSort(), 11)
            | self::protectionBitsDefaultFalse($protection->getAutoFilter(), 12)
            | self::protectionBitsDefaultFalse($protection->getPivotTables(), 13)
            | self::protectionBitsDefaultTrue($protection->getSelectUnlockedCells(), 14);

        // record data
        $recordData = pack(
            'vVVCVVvv',
            0x0867, // repeated record identifier
            0x0000, // not used
            0x0000, // not used
            0x00, // not used
            0x01000200, // unknown data
            0xFFFFFFFF, // unknown data
            $options, // options
            0x0000 // not used
        );

        $length = strlen($recordData);
        $header = pack('vv', $record, $length);

        $this->append($header . $recordData);
    }

    /**
     * Write BIFF record RANGEPROTECTION.
     *
     * Openoffice.org's Documentation of the Microsoft Excel File Format uses term RANGEPROTECTION for these records
     * Microsoft Office Excel 97-2007 Binary File Format Specification uses term FEAT for these records
     */
    private function writeRangeProtection(): void
    {
        foreach ($this->phpSheet->getProtectedCells() as $range => $password) {
            // number of ranges, e.g. 'A1:B3 C20:D25'
            $cellRanges = explode(' ', $range);
            $cref = count($cellRanges);

            $recordData = pack(
                'vvVVvCVvVv',
                0x0868,
                0x00,
                0x0000,
                0x0000,
                0x02,
                0x0,
                0x0000,
                $cref,
                0x0000,
                0x00
            );

            foreach ($cellRanges as $cellRange) {
                $recordData .= $this->writeBIFF8CellRangeAddressFixed($cellRange);
            }

            // the rgbFeat structure
            $recordData .= pack(
                'VV',
                0x0000,
                hexdec($password)
            );

            $recordData .= StringHelper::UTF8toBIFF8UnicodeLong('p' . md5($recordData));

            $length = strlen($recordData);

            $record = 0x0868; // Record identifier
            $header = pack('vv', $record, $length);
            $this->append($header . $recordData);
        }
    }

    /**
     * Writes the Excel BIFF PANE record.
     * The panes can either be frozen or thawed (unfrozen).
     * Frozen panes are specified in terms of an integer number of rows and columns.
     * Thawed panes are specified in terms of Excel's units for rows and columns.
     */
    private function writePanes(): void
    {
        if (!$this->phpSheet->getFreezePane()) {
            // thaw panes
            return;
        }

        [$column, $row] = Coordinate::indexesFromString($this->phpSheet->getFreezePane());
        $x = $column - 1;
        $y = $row - 1;

        [$leftMostColumn, $topRow] = Coordinate::indexesFromString($this->phpSheet->getTopLeftCell() ?? '');
        //Coordinates are zero-based in xls files
        $rwTop = $topRow - 1;
        $colLeft = $leftMostColumn - 1;

        $record = 0x0041; // Record identifier
        $length = 0x000A; // Number of bytes to follow

        // Determine which pane should be active. There is also the undocumented
        // option to override this should it be necessary: may be removed later.
        $pnnAct = 0;
        if ($x != 0 && $y != 0) {
            $pnnAct = 0; // Bottom right
        }
        if ($x != 0 && $y == 0) {
            $pnnAct = 1; // Top right
        }
        if ($x == 0 && $y != 0) {
            $pnnAct = 2; // Bottom left
        }
        if ($x == 0 && $y == 0) {
            $pnnAct = 3; // Top left
        }

        $this->activePane = $pnnAct; // Used in writeSelection

        $header = pack('vv', $record, $length);
        $data = pack('vvvvv', $x, $y, $rwTop, $colLeft, $pnnAct);
        $this->append($header . $data);
    }

    /**
     * Store the page setup SETUP BIFF record.
     */
    private function writeSetup(): void
    {
        $record = 0x00A1; // Record identifier
        $length = 0x0022; // Number of bytes to follow

        $iPaperSize = $this->phpSheet->getPageSetup()->getPaperSize(); // Paper size
        $iScale = $this->phpSheet->getPageSetup()->getScale() ?: 100; // Print scaling factor

        $iPageStart = 0x01; // Starting page number
        $iFitWidth = (int) $this->phpSheet->getPageSetup()->getFitToWidth(); // Fit to number of pages wide
        $iFitHeight = (int) $this->phpSheet->getPageSetup()->getFitToHeight(); // Fit to number of pages high
        $iRes = 0x0258; // Print resolution
        $iVRes = 0x0258; // Vertical print resolution

        $numHdr = $this->phpSheet->getPageMargins()->getHeader(); // Header Margin

        $numFtr = $this->phpSheet->getPageMargins()->getFooter(); // Footer Margin
        $iCopies = 0x01; // Number of copies

        // Order of printing pages
        $fLeftToRight = $this->phpSheet->getPageSetup()->getPageOrder() === PageSetup::PAGEORDER_DOWN_THEN_OVER
            ? 0x0 : 0x1;
        // Page orientation
        $fLandscape = ($this->phpSheet->getPageSetup()->getOrientation() == PageSetup::ORIENTATION_LANDSCAPE)
            ? 0x0 : 0x1;

        $fNoPls = 0x0; // Setup not read from printer
        $fNoColor = 0x0; // Print black and white
        $fDraft = 0x0; // Print draft quality
        $fNotes = 0x0; // Print notes
        $fNoOrient = 0x0; // Orientation not set
        $fUsePage = 0x0; // Use custom starting page

        $grbit = $fLeftToRight;
        $grbit |= $fLandscape << 1;
        $grbit |= $fNoPls << 2;
        $grbit |= $fNoColor << 3;
        $grbit |= $fDraft << 4;
        $grbit |= $fNotes << 5;
        $grbit |= $fNoOrient << 6;
        $grbit |= $fUsePage << 7;

        $numHdr = pack('d', $numHdr);
        $numFtr = pack('d', $numFtr);
        if (self::getByteOrder()) { // if it's Big Endian
            $numHdr = strrev($numHdr);
            $numFtr = strrev($numFtr);
        }

        $header = pack('vv', $record, $length);
        $data1 = pack('vvvvvvvv', $iPaperSize, $iScale, $iPageStart, $iFitWidth, $iFitHeight, $grbit, $iRes, $iVRes);
        $data2 = $numHdr . $numFtr;
        $data3 = pack('v', $iCopies);
        $this->append($header . $data1 . $data2 . $data3);
    }

    /**
     * Store the header caption BIFF record.
     */
    private function writeHeader(): void
    {
        $record = 0x0014; // Record identifier

        /* removing for now
        // need to fix character count (multibyte!)
        if (strlen($this->phpSheet->getHeaderFooter()->getOddHeader()) <= 255) {
            $str      = $this->phpSheet->getHeaderFooter()->getOddHeader();       // header string
        } else {
            $str = '';
        }
        */

        $recordData = StringHelper::UTF8toBIFF8UnicodeLong($this->phpSheet->getHeaderFooter()->getOddHeader());
        $length = strlen($recordData);

        $header = pack('vv', $record, $length);

        $this->append($header . $recordData);
    }

    /**
     * Store the footer caption BIFF record.
     */
    private function writeFooter(): void
    {
        $record = 0x0015; // Record identifier

        /* removing for now
        // need to fix character count (multibyte!)
        if (strlen($this->phpSheet->getHeaderFooter()->getOddFooter()) <= 255) {
            $str = $this->phpSheet->getHeaderFooter()->getOddFooter();
        } else {
            $str = '';
        }
        */

        $recordData = StringHelper::UTF8toBIFF8UnicodeLong($this->phpSheet->getHeaderFooter()->getOddFooter());
        $length = strlen($recordData);

        $header = pack('vv', $record, $length);

        $this->append($header . $recordData);
    }

    /**
     * Store the horizontal centering HCENTER BIFF record.
     */
    private function writeHcenter(): void
    {
        $record = 0x0083; // Record identifier
        $length = 0x0002; // Bytes to follow

        $fHCenter = $this->phpSheet->getPageSetup()->getHorizontalCentered() ? 1 : 0; // Horizontal centering

        $header = pack('vv', $record, $length);
        $data = pack('v', $fHCenter);

        $this->append($header . $data);
    }

    /**
     * Store the vertical centering VCENTER BIFF record.
     */
    private function writeVcenter(): void
    {
        $record = 0x0084; // Record identifier
        $length = 0x0002; // Bytes to follow

        $fVCenter = $this->phpSheet->getPageSetup()->getVerticalCentered() ? 1 : 0; // Horizontal centering

        $header = pack('vv', $record, $length);
        $data = pack('v', $fVCenter);
        $this->append($header . $data);
    }

    /**
     * Store the LEFTMARGIN BIFF record.
     */
    private function writeMarginLeft(): void
    {
        $record = 0x0026; // Record identifier
        $length = 0x0008; // Bytes to follow

        $margin = $this->phpSheet->getPageMargins()->getLeft(); // Margin in inches

        $header = pack('vv', $record, $length);
        $data = pack('d', $margin);
        if (self::getByteOrder()) { // if it's Big Endian
            $data = strrev($data);
        }

        $this->append($header . $data);
    }

    /**
     * Store the RIGHTMARGIN BIFF record.
     */
    private function writeMarginRight(): void
    {
        $record = 0x0027; // Record identifier
        $length = 0x0008; // Bytes to follow

        $margin = $this->phpSheet->getPageMargins()->getRight(); // Margin in inches

        $header = pack('vv', $record, $length);
        $data = pack('d', $margin);
        if (self::getByteOrder()) { // if it's Big Endian
            $data = strrev($data);
        }

        $this->append($header . $data);
    }

    /**
     * Store the TOPMARGIN BIFF record.
     */
    private function writeMarginTop(): void
    {
        $record = 0x0028; // Record identifier
        $length = 0x0008; // Bytes to follow

        $margin = $this->phpSheet->getPageMargins()->getTop(); // Margin in inches

        $header = pack('vv', $record, $length);
        $data = pack('d', $margin);
        if (self::getByteOrder()) { // if it's Big Endian
            $data = strrev($data);
        }

        $this->append($header . $data);
    }

    /**
     * Store the BOTTOMMARGIN BIFF record.
     */
    private function writeMarginBottom(): void
    {
        $record = 0x0029; // Record identifier
        $length = 0x0008; // Bytes to follow

        $margin = $this->phpSheet->getPageMargins()->getBottom(); // Margin in inches

        $header = pack('vv', $record, $length);
        $data = pack('d', $margin);
        if (self::getByteOrder()) { // if it's Big Endian
            $data = strrev($data);
        }

        $this->append($header . $data);
    }

    /**
     * Write the PRINTHEADERS BIFF record.
     */
    private function writePrintHeaders(): void
    {
        $record = 0x002a; // Record identifier
        $length = 0x0002; // Bytes to follow

        $fPrintRwCol = $this->printHeaders; // Boolean flag

        $header = pack('vv', $record, $length);
        $data = pack('v', $fPrintRwCol);
        $this->append($header . $data);
    }

    /**
     * Write the PRINTGRIDLINES BIFF record. Must be used in conjunction with the
     * GRIDSET record.
     */
    private function writePrintGridlines(): void
    {
        $record = 0x002b; // Record identifier
        $length = 0x0002; // Bytes to follow

        $fPrintGrid = $this->phpSheet->getPrintGridlines() ? 1 : 0; // Boolean flag

        $header = pack('vv', $record, $length);
        $data = pack('v', $fPrintGrid);
        $this->append($header . $data);
    }

    /**
     * Write the GRIDSET BIFF record. Must be used in conjunction with the
     * PRINTGRIDLINES record.
     */
    private function writeGridset(): void
    {
        $record = 0x0082; // Record identifier
        $length = 0x0002; // Bytes to follow

        $fGridSet = !$this->phpSheet->getPrintGridlines(); // Boolean flag

        $header = pack('vv', $record, $length);
        $data = pack('v', $fGridSet);
        $this->append($header . $data);
    }

    /**
     * Write the AUTOFILTERINFO BIFF record. This is used to configure the number of autofilter select used in the sheet.
     */
    private function writeAutoFilterInfo(): void
    {
        $record = 0x009D; // Record identifier
        $length = 0x0002; // Bytes to follow

        $rangeBounds = Coordinate::rangeBoundaries($this->phpSheet->getAutoFilter()->getRange());
        $iNumFilters = 1 + $rangeBounds[1][0] - $rangeBounds[0][0];

        $header = pack('vv', $record, $length);
        $data = pack('v', $iNumFilters);
        $this->append($header . $data);
    }

    /**
     * Write the GUTS BIFF record. This is used to configure the gutter margins
     * where Excel outline symbols are displayed. The visibility of the gutters is
     * controlled by a flag in WSBOOL.
     *
     * @see writeWsbool()
     */
    private function writeGuts(): void
    {
        $record = 0x0080; // Record identifier
        $length = 0x0008; // Bytes to follow

        $dxRwGut = 0x0000; // Size of row gutter
        $dxColGut = 0x0000; // Size of col gutter

        // determine maximum row outline level
        $maxRowOutlineLevel = 0;
        foreach ($this->phpSheet->getRowDimensions() as $rowDimension) {
            $maxRowOutlineLevel = max($maxRowOutlineLevel, $rowDimension->getOutlineLevel());
        }

        $col_level = 0;

        // Calculate the maximum column outline level. The equivalent calculation
        // for the row outline level is carried out in writeRow().
        $colcount = count($this->columnInfo);
        for ($i = 0; $i < $colcount; ++$i) {
            $col_level = max($this->columnInfo[$i][5], $col_level);
        }

        // Set the limits for the outline levels (0 <= x <= 7).
        $col_level = max(0, min($col_level, 7));

        // The displayed level is one greater than the max outline levels
        if ($maxRowOutlineLevel) {
            ++$maxRowOutlineLevel;
        }
        if ($col_level) {
            ++$col_level;
        }

        $header = pack('vv', $record, $length);
        $data = pack('vvvv', $dxRwGut, $dxColGut, $maxRowOutlineLevel, $col_level);

        $this->append($header . $data);
    }

    /**
     * Write the WSBOOL BIFF record, mainly for fit-to-page. Used in conjunction
     * with the SETUP record.
     */
    private function writeWsbool(): void
    {
        $record = 0x0081; // Record identifier
        $length = 0x0002; // Bytes to follow
        $grbit = 0x0000;

        // The only option that is of interest is the flag for fit to page. So we
        // set all the options in one go.
        //
        // Set the option flags
        $grbit |= 0x0001; // Auto page breaks visible
        if ($this->outlineStyle) {
            $grbit |= 0x0020; // Auto outline styles
        }
        if ($this->phpSheet->getShowSummaryBelow()) {
            $grbit |= 0x0040; // Outline summary below
        }
        if ($this->phpSheet->getShowSummaryRight()) {
            $grbit |= 0x0080; // Outline summary right
        }
        if ($this->phpSheet->getPageSetup()->getFitToPage()) {
            $grbit |= 0x0100; // Page setup fit to page
        }
        if ($this->outlineOn) {
            $grbit |= 0x0400; // Outline symbols displayed
        }

        $header = pack('vv', $record, $length);
        $data = pack('v', $grbit);
        $this->append($header . $data);
    }

    /**
     * Write the HORIZONTALPAGEBREAKS and VERTICALPAGEBREAKS BIFF records.
     */
    private function writeBreaks(): void
    {
        // initialize
        $vbreaks = [];
        $hbreaks = [];

        foreach ($this->phpSheet->getRowBreaks() as $cell => $break) {
            // Fetch coordinates
            $coordinates = Coordinate::coordinateFromString($cell);
            $hbreaks[] = $coordinates[1];
        }
        foreach ($this->phpSheet->getColumnBreaks() as $cell => $break) {
            // Fetch coordinates
            $coordinates = Coordinate::indexesFromString($cell);
            $vbreaks[] = $coordinates[0] - 1;
        }

        //horizontal page breaks
        if (!empty($hbreaks)) {
            // Sort and filter array of page breaks
            sort($hbreaks, SORT_NUMERIC);
            if ($hbreaks[0] == 0) { // don't use first break if it's 0
                array_shift($hbreaks);
            }

            $record = 0x001b; // Record identifier
            $cbrk = count($hbreaks); // Number of page breaks
            $length = 2 + 6 * $cbrk; // Bytes to follow

            $header = pack('vv', $record, $length);
            $data = pack('v', $cbrk);

            // Append each page break
            foreach ($hbreaks as $hbreak) {
                $data .= pack('vvv', $hbreak, 0x0000, 0x00ff);
            }

            $this->append($header . $data);
        }

        // vertical page breaks
        if (!empty($vbreaks)) {
            // 1000 vertical pagebreaks appears to be an internal Excel 5 limit.
            // It is slightly higher in Excel 97/200, approx. 1026
            $vbreaks = array_slice($vbreaks, 0, 1000);

            // Sort and filter array of page breaks
            sort($vbreaks, SORT_NUMERIC);
            if ($vbreaks[0] == 0) { // don't use first break if it's 0
                array_shift($vbreaks);
            }

            $record = 0x001a; // Record identifier
            $cbrk = count($vbreaks); // Number of page breaks
            $length = 2 + 6 * $cbrk; // Bytes to follow

            $header = pack('vv', $record, $length);
            $data = pack('v', $cbrk);

            // Append each page break
            foreach ($vbreaks as $vbreak) {
                $data .= pack('vvv', $vbreak, 0x0000, 0xffff);
            }

            $this->append($header . $data);
        }
    }

    /**
     * Set the Biff PROTECT record to indicate that the worksheet is protected.
     */
    private function writeProtect(): void
    {
        // Exit unless sheet protection has been specified
        if ($this->phpSheet->getProtection()->getSheet() !== true) {
            return;
        }

        $record = 0x0012; // Record identifier
        $length = 0x0002; // Bytes to follow

        $fLock = 1; // Worksheet is protected

        $header = pack('vv', $record, $length);
        $data = pack('v', $fLock);

        $this->append($header . $data);
    }

    /**
     * Write SCENPROTECT.
     */
    private function writeScenProtect(): void
    {
        // Exit if sheet protection is not active
        if ($this->phpSheet->getProtection()->getSheet() !== true) {
            return;
        }

        // Exit if scenarios are not protected
        if ($this->phpSheet->getProtection()->getScenarios() !== true) {
            return;
        }

        $record = 0x00DD; // Record identifier
        $length = 0x0002; // Bytes to follow

        $header = pack('vv', $record, $length);
        $data = pack('v', 1);

        $this->append($header . $data);
    }

    /**
     * Write OBJECTPROTECT.
     */
    private function writeObjectProtect(): void
    {
        // Exit if sheet protection is not active
        if ($this->phpSheet->getProtection()->getSheet() !== true) {
            return;
        }

        // Exit if objects are not protected
        if ($this->phpSheet->getProtection()->getObjects() !== true) {
            return;
        }

        $record = 0x0063; // Record identifier
        $length = 0x0002; // Bytes to follow

        $header = pack('vv', $record, $length);
        $data = pack('v', 1);

        $this->append($header . $data);
    }

    /**
     * Write the worksheet PASSWORD record.
     */
    private function writePassword(): void
    {
        // Exit unless sheet protection and password have been specified
        if ($this->phpSheet->getProtection()->getSheet() !== true || !$this->phpSheet->getProtection()->getPassword() || $this->phpSheet->getProtection()->getAlgorithm() !== '') {
            return;
        }

        $record = 0x0013; // Record identifier
        $length = 0x0002; // Bytes to follow

        $wPassword = hexdec($this->phpSheet->getProtection()->getPassword()); // Encoded password

        $header = pack('vv', $record, $length);
        $data = pack('v', $wPassword);

        $this->append($header . $data);
    }

    /**
     * Insert a 24bit bitmap image in a worksheet.
     *
     * @param int $row The row we are going to insert the bitmap into
     * @param int $col The column we are going to insert the bitmap into
     * @param mixed $bitmap The bitmap filename or GD-image resource
     * @param int $x the horizontal position (offset) of the image inside the cell
     * @param int $y the vertical position (offset) of the image inside the cell
     * @param float $scale_x The horizontal scale
     * @param float $scale_y The vertical scale
     */
    public function insertBitmap($row, $col, $bitmap, $x = 0, $y = 0, $scale_x = 1, $scale_y = 1): void
    {
        $bitmap_array = (is_resource($bitmap) || $bitmap instanceof GdImage
            ? $this->processBitmapGd($bitmap)
            : $this->processBitmap($bitmap));
        [$width, $height, $size, $data] = $bitmap_array;

        // Scale the frame of the image.
        $width *= $scale_x;
        $height *= $scale_y;

        // Calculate the vertices of the image and write the OBJ record
        $this->positionImage($col, $row, $x, $y, (int) $width, (int) $height);

        // Write the IMDATA record to store the bitmap data
        $record = 0x007f;
        $length = 8 + $size;
        $cf = 0x09;
        $env = 0x01;
        $lcb = $size;

        $header = pack('vvvvV', $record, $length, $cf, $env, $lcb);
        $this->append($header . $data);
    }

    /**
     * Calculate the vertices that define the position of the image as required by
     * the OBJ record.
     *
     *         +------------+------------+
     *         |     A      |      B     |
     *   +-----+------------+------------+
     *   |     |(x1,y1)     |            |
     *   |  1  |(A1)._______|______      |
     *   |     |    |              |     |
     *   |     |    |              |     |
     *   +-----+----|    BITMAP    |-----+
     *   |     |    |              |     |
     *   |  2  |    |______________.     |
     *   |     |            |        (B2)|
     *   |     |            |     (x2,y2)|
     *   +---- +------------+------------+
     *
     * Example of a bitmap that covers some of the area from cell A1 to cell B2.
     *
     * Based on the width and height of the bitmap we need to calculate 8 vars:
     *     $col_start, $row_start, $col_end, $row_end, $x1, $y1, $x2, $y2.
     * The width and height of the cells are also variable and have to be taken into
     * account.
     * The values of $col_start and $row_start are passed in from the calling
     * function. The values of $col_end and $row_end are calculated by subtracting
     * the width and height of the bitmap from the width and height of the
     * underlying cells.
     * The vertices are expressed as a percentage of the underlying cell width as
     * follows (rhs values are in pixels):
     *
     *       x1 = X / W *1024
     *       y1 = Y / H *256
     *       x2 = (X-1) / W *1024
     *       y2 = (Y-1) / H *256
     *
     *       Where:  X is distance from the left side of the underlying cell
     *               Y is distance from the top of the underlying cell
     *               W is the width of the cell
     *               H is the height of the cell
     * The SDK incorrectly states that the height should be expressed as a
     *        percentage of 1024.
     *
     * @param int $col_start Col containing upper left corner of object
     * @param int $row_start Row containing top left corner of object
     * @param int $x1 Distance to left side of object
     * @param int $y1 Distance to top of object
     * @param int $width Width of image frame
     * @param int $height Height of image frame
     */
    public function positionImage($col_start, $row_start, $x1, $y1, $width, $height): void
    {
        // Initialise end cell to the same as the start cell
        $col_end = $col_start; // Col containing lower right corner of object
        $row_end = $row_start; // Row containing bottom right corner of object

        // Zero the specified offset if greater than the cell dimensions
        if ($x1 >= Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_start + 1))) {
            $x1 = 0;
        }
        if ($y1 >= Xls::sizeRow($this->phpSheet, $row_start + 1)) {
            $y1 = 0;
        }

        $width = $width + $x1 - 1;
        $height = $height + $y1 - 1;

        // Subtract the underlying cell widths to find the end cell of the image
        while ($width >= Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_end + 1))) {
            $width -= Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_end + 1));
            ++$col_end;
        }

        // Subtract the underlying cell heights to find the end cell of the image
        while ($height >= Xls::sizeRow($this->phpSheet, $row_end + 1)) {
            $height -= Xls::sizeRow($this->phpSheet, $row_end + 1);
            ++$row_end;
        }

        // Bitmap isn't allowed to start or finish in a hidden cell, i.e. a cell
        // with zero eight or width.
        //
        if (Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_start + 1)) == 0) {
            return;
        }
        if (Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_end + 1)) == 0) {
            return;
        }
        if (Xls::sizeRow($this->phpSheet, $row_start + 1) == 0) {
            return;
        }
        if (Xls::sizeRow($this->phpSheet, $row_end + 1) == 0) {
            return;
        }

        // Convert the pixel values to the percentage value expected by Excel
        $x1 = $x1 / Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_start + 1)) * 1024;
        $y1 = $y1 / Xls::sizeRow($this->phpSheet, $row_start + 1) * 256;
        $x2 = $width / Xls::sizeCol($this->phpSheet, Coordinate::stringFromColumnIndex($col_end + 1)) * 1024; // Distance to right side of object
        $y2 = $height / Xls::sizeRow($this->phpSheet, $row_end + 1) * 256; // Distance to bottom of object

        $this->writeObjPicture($col_start, $x1, $row_start, $y1, $col_end, $x2, $row_end, $y2);
    }

    /**
     * Store the OBJ record that precedes an IMDATA record. This could be generalise
     * to support other Excel objects.
     *
     * @param int $colL Column containing upper left corner of object
     * @param int $dxL Distance from left side of cell
     * @param int $rwT Row containing top left corner of object
     * @param int $dyT Distance from top of cell
     * @param int $colR Column containing lower right corner of object
     * @param int $dxR Distance from right of cell
     * @param int $rwB Row containing bottom right corner of object
     * @param int $dyB Distance from bottom of cell
     */
    private function writeObjPicture($colL, $dxL, $rwT, $dyT, $colR, $dxR, $rwB, $dyB): void
    {
        $record = 0x005d; // Record identifier
        $length = 0x003c; // Bytes to follow

        $cObj = 0x0001; // Count of objects in file (set to 1)
        $OT = 0x0008; // Object type. 8 = Picture
        $id = 0x0001; // Object ID
        $grbit = 0x0614; // Option flags

        $cbMacro = 0x0000; // Length of FMLA structure
        $Reserved1 = 0x0000; // Reserved
        $Reserved2 = 0x0000; // Reserved

        $icvBack = 0x09; // Background colour
        $icvFore = 0x09; // Foreground colour
        $fls = 0x00; // Fill pattern
        $fAuto = 0x00; // Automatic fill
        $icv = 0x08; // Line colour
        $lns = 0xff; // Line style
        $lnw = 0x01; // Line weight
        $fAutoB = 0x00; // Automatic border
        $frs = 0x0000; // Frame style
        $cf = 0x0009; // Image format, 9 = bitmap
        $Reserved3 = 0x0000; // Reserved
        $cbPictFmla = 0x0000; // Length of FMLA structure
        $Reserved4 = 0x0000; // Reserved
        $grbit2 = 0x0001; // Option flags
        $Reserved5 = 0x0000; // Reserved

        $header = pack('vv', $record, $length);
        $data = pack('V', $cObj);
        $data .= pack('v', $OT);
        $data .= pack('v', $id);
        $data .= pack('v', $grbit);
        $data .= pack('v', $colL);
        $data .= pack('v', $dxL);
        $data .= pack('v', $rwT);
        $data .= pack('v', $dyT);
        $data .= pack('v', $colR);
        $data .= pack('v', $dxR);
        $data .= pack('v', $rwB);
        $data .= pack('v', $dyB);
        $data .= pack('v', $cbMacro);
        $data .= pack('V', $Reserved1);
        $data .= pack('v', $Reserved2);
        $data .= pack('C', $icvBack);
        $data .= pack('C', $icvFore);
        $data .= pack('C', $fls);
        $data .= pack('C', $fAuto);
        $data .= pack('C', $icv);
        $data .= pack('C', $lns);
        $data .= pack('C', $lnw);
        $data .= pack('C', $fAutoB);
        $data .= pack('v', $frs);
        $data .= pack('V', $cf);
        $data .= pack('v', $Reserved3);
        $data .= pack('v', $cbPictFmla);
        $data .= pack('v', $Reserved4);
        $data .= pack('v', $grbit2);
        $data .= pack('V', $Reserved5);

        $this->append($header . $data);
    }

    /**
     * Convert a GD-image into the internal format.
     *
     * @param GdImage|resource $image The image to process
     *
     * @return array Array with data and properties of the bitmap
     */
    public function processBitmapGd($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $data = pack('Vvvvv', 0x000c, $width, $height, 0x01, 0x18);
        for ($j = $height; --$j;) {
            for ($i = 0; $i < $width; ++$i) {
                /** @phpstan-ignore-next-line */
                $color = imagecolorsforindex($image, imagecolorat($image, $i, $j));
                if ($color !== false) {
                    foreach (['red', 'green', 'blue'] as $key) {
                        $color[$key] = $color[$key] + (int) round((255 - $color[$key]) * $color['alpha'] / 127);
                    }
                    $data .= chr($color['blue']) . chr($color['green']) . chr($color['red']);
                }
            }
            if (3 * $width % 4) {
                $data .= str_repeat("\x00", 4 - 3 * $width % 4);
            }
        }

        return [$width, $height, strlen($data), $data];
    }

    /**
     * Convert a 24 bit bitmap into the modified internal format used by Windows.
     * This is described in BITMAPCOREHEADER and BITMAPCOREINFO structures in the
     * MSDN library.
     *
     * @param string $bitmap The bitmap to process
     *
     * @return array Array with data and properties of the bitmap
     */
    public function processBitmap($bitmap)
    {
        // Open file.
        $bmp_fd = @fopen($bitmap, 'rb');
        if ($bmp_fd === false) {
            throw new WriterException("Couldn't import $bitmap");
        }

        // Slurp the file into a string.
        $data = (string) fread($bmp_fd, (int) filesize($bitmap));

        // Check that the file is big enough to be a bitmap.
        if (strlen($data) <= 0x36) {
            throw new WriterException("$bitmap doesn't contain enough data.\n");
        }

        // The first 2 bytes are used to identify the bitmap.

        $identity = unpack('A2ident', $data);
        if ($identity === false || $identity['ident'] != 'BM') {
            throw new WriterException("$bitmap doesn't appear to be a valid bitmap image.\n");
        }

        // Remove bitmap data: ID.
        $data = substr($data, 2);

        // Read and remove the bitmap size. This is more reliable than reading
        // the data size at offset 0x22.
        //
        $size_array = unpack('Vsa', substr($data, 0, 4)) ?: [];
        $size = $size_array['sa'];
        $data = substr($data, 4);
        $size -= 0x36; // Subtract size of bitmap header.
        $size += 0x0C; // Add size of BIFF header.

        // Remove bitmap data: reserved, offset, header length.
        $data = substr($data, 12);

        // Read and remove the bitmap width and height. Verify the sizes.
        $width_and_height = unpack('V2', substr($data, 0, 8)) ?: [];
        $width = $width_and_height[1];
        $height = $width_and_height[2];
        $data = substr($data, 8);
        if ($width > 0xFFFF) {
            throw new WriterException("$bitmap: largest image width supported is 65k.\n");
        }
        if ($height > 0xFFFF) {
            throw new WriterException("$bitmap: largest image height supported is 65k.\n");
        }

        // Read and remove the bitmap planes and bpp data. Verify them.
        $planes_and_bitcount = unpack('v2', substr($data, 0, 4));
        $data = substr($data, 4);
        if ($planes_and_bitcount === false || $planes_and_bitcount[2] != 24) { // Bitcount
            throw new WriterException("$bitmap isn't a 24bit true color bitmap.\n");
        }
        if ($planes_and_bitcount[1] != 1) {
            throw new WriterException("$bitmap: only 1 plane supported in bitmap image.\n");
        }

        // Read and remove the bitmap compression. Verify compression.
        $compression = unpack('Vcomp', substr($data, 0, 4));
        $data = substr($data, 4);

        if ($compression === false || $compression['comp'] != 0) {
            throw new WriterException("$bitmap: compression not supported in bitmap image.\n");
        }

        // Remove bitmap data: data size, hres, vres, colours, imp. colours.
        $data = substr($data, 20);

        // Add the BITMAPCOREHEADER data
        $header = pack('Vvvvv', 0x000c, $width, $height, 0x01, 0x18);
        $data = $header . $data;

        return [$width, $height, $size, $data];
    }

    /**
     * Store the window zoom factor. This should be a reduced fraction but for
     * simplicity we will store all fractions with a numerator of 100.
     */
    private function writeZoom(): void
    {
        // If scale is 100 we don't need to write a record
        if ($this->phpSheet->getSheetView()->getZoomScale() == 100) {
            return;
        }

        $record = 0x00A0; // Record identifier
        $length = 0x0004; // Bytes to follow

        $header = pack('vv', $record, $length);
        $data = pack('vv', $this->phpSheet->getSheetView()->getZoomScale(), 100);
        $this->append($header . $data);
    }

    /**
     * Get Escher object.
     */
    public function getEscher(): ?\PhpOffice\PhpSpreadsheet\Shared\Escher
    {
        return $this->escher;
    }

    /**
     * Set Escher object.
     */
    public function setEscher(?\PhpOffice\PhpSpreadsheet\Shared\Escher $escher): void
    {
        $this->escher = $escher;
    }

    /**
     * Write MSODRAWING record.
     */
    private function writeMsoDrawing(): void
    {
        // write the Escher stream if necessary
        if (isset($this->escher)) {
            $writer = new Escher($this->escher);
            $data = $writer->close();
            $spOffsets = $writer->getSpOffsets();
            $spTypes = $writer->getSpTypes();
            // write the neccesary MSODRAWING, OBJ records

            // split the Escher stream
            $spOffsets[0] = 0;
            $nm = count($spOffsets) - 1; // number of shapes excluding first shape
            for ($i = 1; $i <= $nm; ++$i) {
                // MSODRAWING record
                $record = 0x00EC; // Record identifier

                // chunk of Escher stream for one shape
                $dataChunk = substr($data, $spOffsets[$i - 1], $spOffsets[$i] - $spOffsets[$i - 1]);

                $length = strlen($dataChunk);
                $header = pack('vv', $record, $length);

                $this->append($header . $dataChunk);

                // OBJ record
                $record = 0x005D; // record identifier
                $objData = '';

                // ftCmo
                if ($spTypes[$i] == 0x00C9) {
                    // Add ftCmo (common object data) subobject
                    $objData .=
                        pack(
                            'vvvvvVVV',
                            0x0015, // 0x0015 = ftCmo
                            0x0012, // length of ftCmo data
                            0x0014, // object type, 0x0014 = filter
                            $i, // object id number, Excel seems to use 1-based index, local for the sheet
                            0x2101, // option flags, 0x2001 is what OpenOffice.org uses
                            0, // reserved
                            0, // reserved
                            0  // reserved
                        );

                    // Add ftSbs Scroll bar subobject
                    $objData .= pack('vv', 0x00C, 0x0014);
                    $objData .= pack('H*', '0000000000000000640001000A00000010000100');
                    // Add ftLbsData (List box data) subobject
                    $objData .= pack('vv', 0x0013, 0x1FEE);
                    $objData .= pack('H*', '00000000010001030000020008005700');
                } else {
                    // Add ftCmo (common object data) subobject
                    $objData .=
                        pack(
                            'vvvvvVVV',
                            0x0015, // 0x0015 = ftCmo
                            0x0012, // length of ftCmo data
                            0x0008, // object type, 0x0008 = picture
                            $i, // object id number, Excel seems to use 1-based index, local for the sheet
                            0x6011, // option flags, 0x6011 is what OpenOffice.org uses
                            0, // reserved
                            0, // reserved
                            0  // reserved
                        );
                }

                // ftEnd
                $objData .=
                    pack(
                        'vv',
                        0x0000, // 0x0000 = ftEnd
                        0x0000  // length of ftEnd data
                    );

                $length = strlen($objData);
                $header = pack('vv', $record, $length);
                $this->append($header . $objData);
            }
        }
    }

    /**
     * Store the DATAVALIDATIONS and DATAVALIDATION records.
     */
    private function writeDataValidity(): void
    {
        // Datavalidation collection
        $dataValidationCollection = $this->phpSheet->getDataValidationCollection();

        // Write data validations?
        if (!empty($dataValidationCollection)) {
            // DATAVALIDATIONS record
            $record = 0x01B2; // Record identifier
            $length = 0x0012; // Bytes to follow

            $grbit = 0x0000; // Prompt box at cell, no cached validity data at DV records
            $horPos = 0x00000000; // Horizontal position of prompt box, if fixed position
            $verPos = 0x00000000; // Vertical position of prompt box, if fixed position
            $objId = 0xFFFFFFFF; // Object identifier of drop down arrow object, or -1 if not visible

            $header = pack('vv', $record, $length);
            $data = pack('vVVVV', $grbit, $horPos, $verPos, $objId, count($dataValidationCollection));
            $this->append($header . $data);

            // DATAVALIDATION records
            $record = 0x01BE; // Record identifier

            foreach ($dataValidationCollection as $cellCoordinate => $dataValidation) {
                // options
                $options = 0x00000000;

                // data type
                $type = CellDataValidation::type($dataValidation);

                $options |= $type << 0;

                // error style
                $errorStyle = CellDataValidation::errorStyle($dataValidation);

                $options |= $errorStyle << 4;

                // explicit formula?
                if ($type == 0x03 && preg_match('/^\".*\"$/', $dataValidation->getFormula1())) {
                    $options |= 0x01 << 7;
                }

                // empty cells allowed
                $options |= $dataValidation->getAllowBlank() << 8;

                // show drop down
                $options |= (!$dataValidation->getShowDropDown()) << 9;

                // show input message
                $options |= $dataValidation->getShowInputMessage() << 18;

                // show error message
                $options |= $dataValidation->getShowErrorMessage() << 19;

                // condition operator
                $operator = CellDataValidation::operator($dataValidation);

                $options |= $operator << 20;

                $data = pack('V', $options);

                // prompt title
                $promptTitle = $dataValidation->getPromptTitle() !== '' ?
                    $dataValidation->getPromptTitle() : chr(0);
                $data .= StringHelper::UTF8toBIFF8UnicodeLong($promptTitle);

                // error title
                $errorTitle = $dataValidation->getErrorTitle() !== '' ?
                    $dataValidation->getErrorTitle() : chr(0);
                $data .= StringHelper::UTF8toBIFF8UnicodeLong($errorTitle);

                // prompt text
                $prompt = $dataValidation->getPrompt() !== '' ?
                    $dataValidation->getPrompt() : chr(0);
                $data .= StringHelper::UTF8toBIFF8UnicodeLong($prompt);

                // error text
                $error = $dataValidation->getError() !== '' ?
                    $dataValidation->getError() : chr(0);
                $data .= StringHelper::UTF8toBIFF8UnicodeLong($error);

                // formula 1
                try {
                    $formula1 = $dataValidation->getFormula1();
                    if ($type == 0x03) { // list type
                        $formula1 = str_replace(',', chr(0), $formula1);
                    }
                    $this->parser->parse($formula1);
                    $formula1 = $this->parser->toReversePolish();
                    $sz1 = strlen($formula1);
                } catch (PhpSpreadsheetException $e) {
                    $sz1 = 0;
                    $formula1 = '';
                }
                $data .= pack('vv', $sz1, 0x0000);
                $data .= $formula1;

                // formula 2
                try {
                    $formula2 = $dataValidation->getFormula2();
                    if ($formula2 === '') {
                        throw new WriterException('No formula2');
                    }
                    $this->parser->parse($formula2);
                    $formula2 = $this->parser->toReversePolish();
                    $sz2 = strlen($formula2);
                } catch (PhpSpreadsheetException $e) {
                    $sz2 = 0;
                    $formula2 = '';
                }
                $data .= pack('vv', $sz2, 0x0000);
                $data .= $formula2;

                // cell range address list
                $data .= pack('v', 0x0001);
                $data .= $this->writeBIFF8CellRangeAddressFixed($cellCoordinate);

                $length = strlen($data);
                $header = pack('vv', $record, $length);

                $this->append($header . $data);
            }
        }
    }

    /**
     * Write PLV Record.
     */
    private function writePageLayoutView(): void
    {
        $record = 0x088B; // Record identifier
        $length = 0x0010; // Bytes to follow

        $rt = 0x088B; // 2
        $grbitFrt = 0x0000; // 2
        //$reserved = 0x0000000000000000; // 8
        $wScalvePLV = $this->phpSheet->getSheetView()->getZoomScale(); // 2

        // The options flags that comprise $grbit
        if ($this->phpSheet->getSheetView()->getView() == SheetView::SHEETVIEW_PAGE_LAYOUT) {
            $fPageLayoutView = 1;
        } else {
            $fPageLayoutView = 0;
        }
        $fRulerVisible = 0;
        $fWhitespaceHidden = 0;

        $grbit = $fPageLayoutView; // 2
        $grbit |= $fRulerVisible << 1;
        $grbit |= $fWhitespaceHidden << 3;

        $header = pack('vv', $record, $length);
        $data = pack('vvVVvv', $rt, $grbitFrt, 0x00000000, 0x00000000, $wScalvePLV, $grbit);
        $this->append($header . $data);
    }

    /**
     * Write CFRule Record.
     */
    private function writeCFRule(
        ConditionalHelper $conditionalFormulaHelper,
        Conditional $conditional,
        string $cellRange
    ): void {
        $record = 0x01B1; // Record identifier
        $type = null; // Type of the CF
        $operatorType = null; // Comparison operator

        if ($conditional->getConditionType() == Conditional::CONDITION_EXPRESSION) {
            $type = 0x02;
            $operatorType = 0x00;
        } elseif ($conditional->getConditionType() == Conditional::CONDITION_CELLIS) {
            $type = 0x01;

            switch ($conditional->getOperatorType()) {
                case Conditional::OPERATOR_NONE:
                    $operatorType = 0x00;

                    break;
                case Conditional::OPERATOR_EQUAL:
                    $operatorType = 0x03;

                    break;
                case Conditional::OPERATOR_GREATERTHAN:
                    $operatorType = 0x05;

                    break;
                case Conditional::OPERATOR_GREATERTHANOREQUAL:
                    $operatorType = 0x07;

                    break;
                case Conditional::OPERATOR_LESSTHAN:
                    $operatorType = 0x06;

                    break;
                case Conditional::OPERATOR_LESSTHANOREQUAL:
                    $operatorType = 0x08;

                    break;
                case Conditional::OPERATOR_NOTEQUAL:
                    $operatorType = 0x04;

                    break;
                case Conditional::OPERATOR_BETWEEN:
                    $operatorType = 0x01;

                    break;
                    // not OPERATOR_NOTBETWEEN 0x02
            }
        }

        // $szValue1 : size of the formula data for first value or formula
        // $szValue2 : size of the formula data for second value or formula
        $arrConditions = $conditional->getConditions();
        $numConditions = count($arrConditions);

        $szValue1 = 0x0000;
        $szValue2 = 0x0000;
        $operand1 = null;
        $operand2 = null;

        if ($numConditions === 1) {
            $conditionalFormulaHelper->processCondition($arrConditions[0], $cellRange);
            $szValue1 = $conditionalFormulaHelper->size();
            $operand1 = $conditionalFormulaHelper->tokens();
        } elseif ($numConditions === 2 && ($conditional->getOperatorType() === Conditional::OPERATOR_BETWEEN)) {
            $conditionalFormulaHelper->processCondition($arrConditions[0], $cellRange);
            $szValue1 = $conditionalFormulaHelper->size();
            $operand1 = $conditionalFormulaHelper->tokens();
            $conditionalFormulaHelper->processCondition($arrConditions[1], $cellRange);
            $szValue2 = $conditionalFormulaHelper->size();
            $operand2 = $conditionalFormulaHelper->tokens();
        }

        // $flags : Option flags
        // Alignment
        $bAlignHz = ($conditional->getStyle()->getAlignment()->getHorizontal() === null ? 1 : 0);
        $bAlignVt = ($conditional->getStyle()->getAlignment()->getVertical() === null ? 1 : 0);
        $bAlignWrapTx = ($conditional->getStyle()->getAlignment()->getWrapText() === false ? 1 : 0);
        $bTxRotation = ($conditional->getStyle()->getAlignment()->getTextRotation() === null ? 1 : 0);
        $bIndent = ($conditional->getStyle()->getAlignment()->getIndent() === 0 ? 1 : 0);
        $bShrinkToFit = ($conditional->getStyle()->getAlignment()->getShrinkToFit() === false ? 1 : 0);
        if ($bAlignHz == 0 || $bAlignVt == 0 || $bAlignWrapTx == 0 || $bTxRotation == 0 || $bIndent == 0 || $bShrinkToFit == 0) {
            $bFormatAlign = 1;
        } else {
            $bFormatAlign = 0;
        }
        // Protection
        $bProtLocked = ($conditional->getStyle()->getProtection()->getLocked() == null ? 1 : 0);
        $bProtHidden = ($conditional->getStyle()->getProtection()->getHidden() == null ? 1 : 0);
        if ($bProtLocked == 0 || $bProtHidden == 0) {
            $bFormatProt = 1;
        } else {
            $bFormatProt = 0;
        }
        // Border
        $bBorderLeft = ($conditional->getStyle()->getBorders()->getLeft()->getBorderStyle() !== Border::BORDER_OMIT) ? 1 : 0;
        $bBorderRight = ($conditional->getStyle()->getBorders()->getRight()->getBorderStyle() !== Border::BORDER_OMIT) ? 1 : 0;
        $bBorderTop = ($conditional->getStyle()->getBorders()->getTop()->getBorderStyle() !== Border::BORDER_OMIT) ? 1 : 0;
        $bBorderBottom = ($conditional->getStyle()->getBorders()->getBottom()->getBorderStyle() !== Border::BORDER_OMIT) ? 1 : 0;
        if ($bBorderLeft === 1 || $bBorderRight === 1 || $bBorderTop === 1 || $bBorderBottom === 1) {
            $bFormatBorder = 1;
        } else {
            $bFormatBorder = 0;
        }
        // Pattern
        $bFillStyle = ($conditional->getStyle()->getFill()->getFillType() === null ? 0 : 1);
        $bFillColor = ($conditional->getStyle()->getFill()->getStartColor()->getARGB() === null ? 0 : 1);
        $bFillColorBg = ($conditional->getStyle()->getFill()->getEndColor()->getARGB() === null ? 0 : 1);
        if ($bFillStyle == 1 || $bFillColor == 1 || $bFillColorBg == 1) {
            $bFormatFill = 1;
        } else {
            $bFormatFill = 0;
        }
        // Font
        if (
            $conditional->getStyle()->getFont()->getName() !== null
            || $conditional->getStyle()->getFont()->getSize() !== null
            || $conditional->getStyle()->getFont()->getBold() !== null
            || $conditional->getStyle()->getFont()->getItalic() !== null
            || $conditional->getStyle()->getFont()->getSuperscript() !== null
            || $conditional->getStyle()->getFont()->getSubscript() !== null
            || $conditional->getStyle()->getFont()->getUnderline() !== null
            || $conditional->getStyle()->getFont()->getStrikethrough() !== null
            || $conditional->getStyle()->getFont()->getColor()->getARGB() !== null
        ) {
            $bFormatFont = 1;
        } else {
            $bFormatFont = 0;
        }
        // Alignment
        $flags = 0;
        $flags |= (1 == $bAlignHz ? 0x00000001 : 0);
        $flags |= (1 == $bAlignVt ? 0x00000002 : 0);
        $flags |= (1 == $bAlignWrapTx ? 0x00000004 : 0);
        $flags |= (1 == $bTxRotation ? 0x00000008 : 0);
        // Justify last line flag
        $flags |= (1 == self::$always1 ? 0x00000010 : 0);
        $flags |= (1 == $bIndent ? 0x00000020 : 0);
        $flags |= (1 == $bShrinkToFit ? 0x00000040 : 0);
        // Default
        $flags |= (1 == self::$always1 ? 0x00000080 : 0);
        // Protection
        $flags |= (1 == $bProtLocked ? 0x00000100 : 0);
        $flags |= (1 == $bProtHidden ? 0x00000200 : 0);
        // Border
        $flags |= (1 == $bBorderLeft ? 0x00000400 : 0);
        $flags |= (1 == $bBorderRight ? 0x00000800 : 0);
        $flags |= (1 == $bBorderTop ? 0x00001000 : 0);
        $flags |= (1 == $bBorderBottom ? 0x00002000 : 0);
        $flags |= (1 == self::$always1 ? 0x00004000 : 0); // Top left to Bottom right border
        $flags |= (1 == self::$always1 ? 0x00008000 : 0); // Bottom left to Top right border
        // Pattern
        $flags |= (1 == $bFillStyle ? 0x00010000 : 0);
        $flags |= (1 == $bFillColor ? 0x00020000 : 0);
        $flags |= (1 == $bFillColorBg ? 0x00040000 : 0);
        $flags |= (1 == self::$always1 ? 0x00380000 : 0);
        // Font
        $flags |= (1 == $bFormatFont ? 0x04000000 : 0);
        // Alignment:
        $flags |= (1 == $bFormatAlign ? 0x08000000 : 0);
        // Border
        $flags |= (1 == $bFormatBorder ? 0x10000000 : 0);
        // Pattern
        $flags |= (1 == $bFormatFill ? 0x20000000 : 0);
        // Protection
        $flags |= (1 == $bFormatProt ? 0x40000000 : 0);
        // Text direction
        $flags |= (1 == self::$always0 ? 0x80000000 : 0);

        $dataBlockFont = null;
        $dataBlockAlign = null;
        $dataBlockBorder = null;
        $dataBlockFill = null;

        // Data Blocks
        if ($bFormatFont == 1) {
            // Font Name
            if ($conditional->getStyle()->getFont()->getName() === null) {
                $dataBlockFont = pack('VVVVVVVV', 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000);
                $dataBlockFont .= pack('VVVVVVVV', 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000, 0x00000000);
            } else {
                $dataBlockFont = StringHelper::UTF8toBIFF8UnicodeLong($conditional->getStyle()->getFont()->getName());
            }
            // Font Size
            if ($conditional->getStyle()->getFont()->getSize() === null) {
                $dataBlockFont .= pack('V', 20 * 11);
            } else {
                $dataBlockFont .= pack('V', 20 * $conditional->getStyle()->getFont()->getSize());
            }
            // Font Options
            $dataBlockFont .= pack('V', 0);
            // Font weight
            if ($conditional->getStyle()->getFont()->getBold() === true) {
                $dataBlockFont .= pack('v', 0x02BC);
            } else {
                $dataBlockFont .= pack('v', 0x0190);
            }
            // Escapement type
            if ($conditional->getStyle()->getFont()->getSubscript() === true) {
                $dataBlockFont .= pack('v', 0x02);
                $fontEscapement = 0;
            } elseif ($conditional->getStyle()->getFont()->getSuperscript() === true) {
                $dataBlockFont .= pack('v', 0x01);
                $fontEscapement = 0;
            } else {
                $dataBlockFont .= pack('v', 0x00);
                $fontEscapement = 1;
            }
            // Underline type
            switch ($conditional->getStyle()->getFont()->getUnderline()) {
                case \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_NONE:
                    $dataBlockFont .= pack('C', 0x00);
                    $fontUnderline = 0;

                    break;
                case \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLE:
                    $dataBlockFont .= pack('C', 0x02);
                    $fontUnderline = 0;

                    break;
                case \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_DOUBLEACCOUNTING:
                    $dataBlockFont .= pack('C', 0x22);
                    $fontUnderline = 0;

                    break;
                case \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE:
                    $dataBlockFont .= pack('C', 0x01);
                    $fontUnderline = 0;

                    break;
                case \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLEACCOUNTING:
                    $dataBlockFont .= pack('C', 0x21);
                    $fontUnderline = 0;

                    break;
                default:
                    $dataBlockFont .= pack('C', 0x00);
                    $fontUnderline = 1;

                    break;
            }
            // Not used (3)
            $dataBlockFont .= pack('vC', 0x0000, 0x00);
            // Font color index
            $colorIdx = Style\ColorMap::lookup($conditional->getStyle()->getFont()->getColor(), 0x00);

            $dataBlockFont .= pack('V', $colorIdx);
            // Not used (4)
            $dataBlockFont .= pack('V', 0x00000000);
            // Options flags for modified font attributes
            $optionsFlags = 0;
            $optionsFlagsBold = ($conditional->getStyle()->getFont()->getBold() === null ? 1 : 0);
            $optionsFlags |= (1 == $optionsFlagsBold ? 0x00000002 : 0);
            $optionsFlags |= (1 == self::$always1 ? 0x00000008 : 0);
            $optionsFlags |= (1 == self::$always1 ? 0x00000010 : 0);
            $optionsFlags |= (1 == self::$always0 ? 0x00000020 : 0);
            $optionsFlags |= (1 == self::$always1 ? 0x00000080 : 0);
            $dataBlockFont .= pack('V', $optionsFlags);
            // Escapement type
            $dataBlockFont .= pack('V', $fontEscapement);
            // Underline type
            $dataBlockFont .= pack('V', $fontUnderline);
            // Always
            $dataBlockFont .= pack('V', 0x00000000);
            // Always
            $dataBlockFont .= pack('V', 0x00000000);
            // Not used (8)
            $dataBlockFont .= pack('VV', 0x00000000, 0x00000000);
            // Always
            $dataBlockFont .= pack('v', 0x0001);
        }
        if ($bFormatAlign === 1) {
            // Alignment and text break
            $blockAlign = Style\CellAlignment::horizontal($conditional->getStyle()->getAlignment());
            $blockAlign |= Style\CellAlignment::wrap($conditional->getStyle()->getAlignment()) << 3;
            $blockAlign |= Style\CellAlignment::vertical($conditional->getStyle()->getAlignment()) << 4;
            $blockAlign |= 0 << 7;

            // Text rotation angle
            $blockRotation = $conditional->getStyle()->getAlignment()->getTextRotation();

            // Indentation
            $blockIndent = $conditional->getStyle()->getAlignment()->getIndent();
            if ($conditional->getStyle()->getAlignment()->getShrinkToFit() === true) {
                $blockIndent |= 1 << 4;
            } else {
                $blockIndent |= 0 << 4;
            }
            $blockIndent |= 0 << 6;

            // Relative indentation
            $blockIndentRelative = 255;

            $dataBlockAlign = pack('CCvvv', $blockAlign, $blockRotation, $blockIndent, $blockIndentRelative, 0x0000);
        }
        if ($bFormatBorder === 1) {
            $blockLineStyle = Style\CellBorder::style($conditional->getStyle()->getBorders()->getLeft());
            $blockLineStyle |= Style\CellBorder::style($conditional->getStyle()->getBorders()->getRight()) << 4;
            $blockLineStyle |= Style\CellBorder::style($conditional->getStyle()->getBorders()->getTop()) << 8;
            $blockLineStyle |= Style\CellBorder::style($conditional->getStyle()->getBorders()->getBottom()) << 12;

            // TODO writeCFRule() => $blockLineStyle => Index Color for left line
            // TODO writeCFRule() => $blockLineStyle => Index Color for right line
            // TODO writeCFRule() => $blockLineStyle => Top-left to bottom-right on/off
            // TODO writeCFRule() => $blockLineStyle => Bottom-left to top-right on/off
            $blockColor = 0;
            // TODO writeCFRule() => $blockColor => Index Color for top line
            // TODO writeCFRule() => $blockColor => Index Color for bottom line
            // TODO writeCFRule() => $blockColor => Index Color for diagonal line
            $blockColor |= Style\CellBorder::style($conditional->getStyle()->getBorders()->getDiagonal()) << 21;
            $dataBlockBorder = pack('vv', $blockLineStyle, $blockColor);
        }
        if ($bFormatFill === 1) {
            // Fill Pattern Style
            $blockFillPatternStyle = Style\CellFill::style($conditional->getStyle()->getFill());
            // Background Color
            $colorIdxBg = Style\ColorMap::lookup($conditional->getStyle()->getFill()->getStartColor(), 0x41);
            // Foreground Color
            $colorIdxFg = Style\ColorMap::lookup($conditional->getStyle()->getFill()->getEndColor(), 0x40);

            $dataBlockFill = pack('v', $blockFillPatternStyle);
            $dataBlockFill .= pack('v', $colorIdxFg | ($colorIdxBg << 7));
        }

        $data = pack('CCvvVv', $type, $operatorType, $szValue1, $szValue2, $flags, 0x0000);
        if ($bFormatFont === 1) { // Block Formatting : OK
            $data .= $dataBlockFont;
        }
        if ($bFormatAlign === 1) {
            $data .= $dataBlockAlign;
        }
        if ($bFormatBorder === 1) {
            $data .= $dataBlockBorder;
        }
        if ($bFormatFill === 1) { // Block Formatting : OK
            $data .= $dataBlockFill;
        }
        if ($bFormatProt == 1) {
            $data .= $this->getDataBlockProtection($conditional);
        }
        if ($operand1 !== null) {
            $data .= $operand1;
        }
        if ($operand2 !== null) {
            $data .= $operand2;
        }
        $header = pack('vv', $record, strlen($data));
        $this->append($header . $data);
    }

    /**
     * Write CFHeader record.
     *
     * @param Conditional[] $conditionalStyles
     */
    private function writeCFHeader(string $cellCoordinate, array $conditionalStyles): bool
    {
        $record = 0x01B0; // Record identifier
        $length = 0x0016; // Bytes to follow

        $numColumnMin = null;
        $numColumnMax = null;
        $numRowMin = null;
        $numRowMax = null;

        $arrConditional = [];
        foreach ($conditionalStyles as $conditional) {
            if (!in_array($conditional->getHashCode(), $arrConditional)) {
                $arrConditional[] = $conditional->getHashCode();
            }
            // Cells
            $rangeCoordinates = Coordinate::rangeBoundaries($cellCoordinate);
            if ($numColumnMin === null || ($numColumnMin > $rangeCoordinates[0][0])) {
                $numColumnMin = $rangeCoordinates[0][0];
            }
            if ($numColumnMax === null || ($numColumnMax < $rangeCoordinates[1][0])) {
                $numColumnMax = $rangeCoordinates[1][0];
            }
            if ($numRowMin === null || ($numRowMin > $rangeCoordinates[0][1])) {
                $numRowMin = (int) $rangeCoordinates[0][1];
            }
            if ($numRowMax === null || ($numRowMax < $rangeCoordinates[1][1])) {
                $numRowMax = (int) $rangeCoordinates[1][1];
            }
        }

        if (count($arrConditional) === 0) {
            return false;
        }

        $needRedraw = 1;
        $cellRange = pack('vvvv', $numRowMin - 1, $numRowMax - 1, $numColumnMin - 1, $numColumnMax - 1);

        $header = pack('vv', $record, $length);
        $data = pack('vv', count($arrConditional), $needRedraw);
        $data .= $cellRange;
        $data .= pack('v', 0x0001);
        $data .= $cellRange;
        $this->append($header . $data);

        return true;
    }

    private function getDataBlockProtection(Conditional $conditional): int
    {
        $dataBlockProtection = 0;
        if ($conditional->getStyle()->getProtection()->getLocked() == Protection::PROTECTION_PROTECTED) {
            $dataBlockProtection = 1;
        }
        if ($conditional->getStyle()->getProtection()->getHidden() == Protection::PROTECTION_PROTECTED) {
            $dataBlockProtection = 1 << 1;
        }

        return $dataBlockProtection;
    }
}
