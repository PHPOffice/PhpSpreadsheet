<?php

namespace PhpOffice\PhpSpreadsheet\Writer\Xls;

use Composer\Pcre\Preg;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\DefinedName;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;

// Original file header of PEAR::Spreadsheet_Excel_Writer_Workbook (used as the base for this class):
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
class Workbook extends BIFFwriter
{
    /**
     * Formula parser.
     */
    private Parser $parser;

    /*
     * The BIFF file size for the workbook. Not currently used.
     *
     * @see calcSheetOffsets()
     */
    //private int $biffSize;

    /**
     * XF Writers.
     *
     * @var Xf[]
     */
    private array $xfWriters = [];

    /**
     * Array containing the colour palette.
     *
     * @var array<int, array{int, int, int, int}>
     */
    private array $palette;

    /**
     * The codepage indicates the text encoding used for strings.
     */
    private int $codepage;

    /**
     * The country code used for localization.
     */
    private int $countryCode;

    /**
     * Workbook.
     */
    private Spreadsheet $spreadsheet;

    /**
     * Fonts writers.
     *
     * @var Font[]
     */
    private array $fontWriters = [];

    /**
     * Added fonts. Maps from font's hash => index in workbook.
     *
     * @var int[]
     */
    private array $addedFonts = [];

    /**
     * Shared number formats.
     *
     * @var NumberFormat[]
     */
    private array $numberFormats = [];

    /**
     * Added number formats. Maps from numberFormat's hash => index in workbook.
     *
     * @var int[]
     */
    private array $addedNumberFormats = [];

    /**
     * Sizes of the binary worksheet streams.
     *
     * @var int[]
     */
    private array $worksheetSizes = [];

    /**
     * Offsets of the binary worksheet streams relative to the start of the global workbook stream.
     *
     * @var int[]
     */
    private array $worksheetOffsets = [];

    /**
     * Total number of shared strings in workbook.
     */
    private int $stringTotal;

    /**
     * Number of unique shared strings in workbook.
     */
    private int $stringUnique;

    /**
     * Array of unique shared strings in workbook.
     *
     * @var array<string, int>
     */
    private array $stringTable;

    /**
     * Color cache.
     *
     * @var int[]
     */
    private array $colors;

    /**
     * Escher object corresponding to MSODRAWINGGROUP.
     */
    private ?\PhpOffice\PhpSpreadsheet\Shared\Escher $escher = null;

    /**
     * Class constructor.
     *
     * @param Spreadsheet $spreadsheet The Workbook
     * @param int $str_total Total number of strings
     * @param int $str_unique Total number of unique strings
     * @param array<string, int> $str_table String Table
     * @param int[] $colors Colour Table
     * @param Parser $parser The formula parser created for the Workbook
     */
    public function __construct(Spreadsheet $spreadsheet, int &$str_total, int &$str_unique, array &$str_table, array &$colors, Parser $parser)
    {
        // It needs to call its parent's constructor explicitly
        parent::__construct();

        $this->parser = $parser;
        //$this->biffSize = 0;
        $this->palette = [];
        $this->countryCode = -1;

        $this->stringTotal = &$str_total;
        $this->stringUnique = &$str_unique;
        $this->stringTable = &$str_table;
        $this->colors = &$colors;
        $this->setPaletteXl97();

        $this->spreadsheet = $spreadsheet;

        $this->codepage = 0x04B0;

        // Add empty sheets and Build color cache
        $countSheets = $spreadsheet->getSheetCount();
        for ($i = 0; $i < $countSheets; ++$i) {
            $phpSheet = $spreadsheet->getSheet($i);

            $this->parser->setExtSheet($phpSheet->getTitle(), $i); // Register worksheet name with parser

            $supbook_index = 0x00;
            $ref = pack('vvv', $supbook_index, $i, $i);
            $this->parser->references[] = $ref; // Register reference with parser

            // Sheet tab colors?
            if ($phpSheet->isTabColorSet()) {
                $this->addColor($phpSheet->getTabColor()->getRGB());
            }
        }
    }

    /**
     * Add a new XF writer.
     *
     * @param bool $isStyleXf Is it a style XF?
     *
     * @return int Index to XF record
     */
    public function addXfWriter(Style $style, bool $isStyleXf = false): int
    {
        $xfWriter = new Xf($style);
        $xfWriter->setIsStyleXf($isStyleXf);

        // Add the font if not already added
        $fontIndex = $this->addFont($style->getFont());

        // Assign the font index to the xf record
        $xfWriter->setFontIndex($fontIndex);

        // Background colors, best to treat these after the font so black will come after white in custom palette
        if ($style->getFill()->getStartColor()->getRGB()) {
            $xfWriter->setFgColor(
                $this->addColor(
                    $style->getFill()->getStartColor()->getRGB()
                )
            );
        }
        if ($style->getFill()->getEndColor()->getRGB()) {
            $xfWriter->setBgColor(
                $this->addColor(
                    $style->getFill()->getEndColor()->getRGB()
                )
            );
        }
        $xfWriter->setBottomColor($this->addColor($style->getBorders()->getBottom()->getColor()->getRGB()));
        $xfWriter->setTopColor($this->addColor($style->getBorders()->getTop()->getColor()->getRGB()));
        $xfWriter->setRightColor($this->addColor($style->getBorders()->getRight()->getColor()->getRGB()));
        $xfWriter->setLeftColor($this->addColor($style->getBorders()->getLeft()->getColor()->getRGB()));
        $xfWriter->setDiagColor($this->addColor($style->getBorders()->getDiagonal()->getColor()->getRGB()));

        // Add the number format if it is not a built-in one and not already added
        if ($style->getNumberFormat()->getBuiltInFormatCode() === false) {
            $numberFormatHashCode = $style->getNumberFormat()->getHashCode();

            if (isset($this->addedNumberFormats[$numberFormatHashCode])) {
                $numberFormatIndex = $this->addedNumberFormats[$numberFormatHashCode];
            } else {
                $numberFormatIndex = 164 + count($this->numberFormats);
                $this->numberFormats[$numberFormatIndex] = $style->getNumberFormat();
                $this->addedNumberFormats[$numberFormatHashCode] = $numberFormatIndex;
            }
        } else {
            $numberFormatIndex = (int) $style->getNumberFormat()->getBuiltInFormatCode();
        }

        // Assign the number format index to xf record
        $xfWriter->setNumberFormatIndex($numberFormatIndex);

        $this->xfWriters[] = $xfWriter;

        return count($this->xfWriters) - 1;
    }

    /**
     * Add a font to added fonts.
     *
     * @return int Index to FONT record
     */
    public function addFont(\PhpOffice\PhpSpreadsheet\Style\Font $font): int
    {
        $fontHashCode = $font->getHashCode();
        if (isset($this->addedFonts[$fontHashCode])) {
            $fontIndex = $this->addedFonts[$fontHashCode];
        } else {
            $countFonts = count($this->fontWriters);
            $fontIndex = ($countFonts < 4) ? $countFonts : $countFonts + 1;

            $fontWriter = new Font($font);
            $fontWriter->setColorIndex($this->addColor($font->getColor()->getRGB()));
            $this->fontWriters[] = $fontWriter;

            $this->addedFonts[$fontHashCode] = $fontIndex;
        }

        return $fontIndex;
    }

    /**
     * Alter color palette adding a custom color.
     *
     * @param string $rgb E.g. 'FF00AA'
     *
     * @return int Color index
     */
    public function addColor(string $rgb, int $default = 0): int
    {
        if (!isset($this->colors[$rgb])) {
            $color
                = [
                    (int) hexdec(substr($rgb, 0, 2)),
                    (int) hexdec(substr($rgb, 2, 2)),
                    (int) hexdec(substr($rgb, 4)),
                    0,
                ];
            $colorIndex = array_search($color, $this->palette);
            if ($colorIndex) {
                $this->colors[$rgb] = $colorIndex;
            } else {
                if (count($this->colors) === 0) {
                    $lastColor = 7;
                } else {
                    $lastColor = end($this->colors);
                }
                if ($lastColor < 57) {
                    // then we add a custom color altering the palette
                    $colorIndex = $lastColor + 1;
                    $this->palette[$colorIndex] = $color;
                    $this->colors[$rgb] = $colorIndex;
                } else {
                    // no room for more custom colors, just map to black
                    $colorIndex = $default;
                }
            }
        } else {
            // fetch already added custom color
            $colorIndex = $this->colors[$rgb];
        }

        return $colorIndex;
    }

    /**
     * Sets the colour palette to the Excel 97+ default.
     */
    private function setPaletteXl97(): void
    {
        $this->palette = [
            0x08 => [0x00, 0x00, 0x00, 0x00],
            0x09 => [0xFF, 0xFF, 0xFF, 0x00],
            0x0A => [0xFF, 0x00, 0x00, 0x00],
            0x0B => [0x00, 0xFF, 0x00, 0x00],
            0x0C => [0x00, 0x00, 0xFF, 0x00],
            0x0D => [0xFF, 0xFF, 0x00, 0x00],
            0x0E => [0xFF, 0x00, 0xFF, 0x00],
            0x0F => [0x00, 0xFF, 0xFF, 0x00],
            0x10 => [0x80, 0x00, 0x00, 0x00],
            0x11 => [0x00, 0x80, 0x00, 0x00],
            0x12 => [0x00, 0x00, 0x80, 0x00],
            0x13 => [0x80, 0x80, 0x00, 0x00],
            0x14 => [0x80, 0x00, 0x80, 0x00],
            0x15 => [0x00, 0x80, 0x80, 0x00],
            0x16 => [0xC0, 0xC0, 0xC0, 0x00],
            0x17 => [0x80, 0x80, 0x80, 0x00],
            0x18 => [0x99, 0x99, 0xFF, 0x00],
            0x19 => [0x99, 0x33, 0x66, 0x00],
            0x1A => [0xFF, 0xFF, 0xCC, 0x00],
            0x1B => [0xCC, 0xFF, 0xFF, 0x00],
            0x1C => [0x66, 0x00, 0x66, 0x00],
            0x1D => [0xFF, 0x80, 0x80, 0x00],
            0x1E => [0x00, 0x66, 0xCC, 0x00],
            0x1F => [0xCC, 0xCC, 0xFF, 0x00],
            0x20 => [0x00, 0x00, 0x80, 0x00],
            0x21 => [0xFF, 0x00, 0xFF, 0x00],
            0x22 => [0xFF, 0xFF, 0x00, 0x00],
            0x23 => [0x00, 0xFF, 0xFF, 0x00],
            0x24 => [0x80, 0x00, 0x80, 0x00],
            0x25 => [0x80, 0x00, 0x00, 0x00],
            0x26 => [0x00, 0x80, 0x80, 0x00],
            0x27 => [0x00, 0x00, 0xFF, 0x00],
            0x28 => [0x00, 0xCC, 0xFF, 0x00],
            0x29 => [0xCC, 0xFF, 0xFF, 0x00],
            0x2A => [0xCC, 0xFF, 0xCC, 0x00],
            0x2B => [0xFF, 0xFF, 0x99, 0x00],
            0x2C => [0x99, 0xCC, 0xFF, 0x00],
            0x2D => [0xFF, 0x99, 0xCC, 0x00],
            0x2E => [0xCC, 0x99, 0xFF, 0x00],
            0x2F => [0xFF, 0xCC, 0x99, 0x00],
            0x30 => [0x33, 0x66, 0xFF, 0x00],
            0x31 => [0x33, 0xCC, 0xCC, 0x00],
            0x32 => [0x99, 0xCC, 0x00, 0x00],
            0x33 => [0xFF, 0xCC, 0x00, 0x00],
            0x34 => [0xFF, 0x99, 0x00, 0x00],
            0x35 => [0xFF, 0x66, 0x00, 0x00],
            0x36 => [0x66, 0x66, 0x99, 0x00],
            0x37 => [0x96, 0x96, 0x96, 0x00],
            0x38 => [0x00, 0x33, 0x66, 0x00],
            0x39 => [0x33, 0x99, 0x66, 0x00],
            0x3A => [0x00, 0x33, 0x00, 0x00],
            0x3B => [0x33, 0x33, 0x00, 0x00],
            0x3C => [0x99, 0x33, 0x00, 0x00],
            0x3D => [0x99, 0x33, 0x66, 0x00],
            0x3E => [0x33, 0x33, 0x99, 0x00],
            0x3F => [0x33, 0x33, 0x33, 0x00],
        ];
    }

    /**
     * Assemble worksheets into a workbook and send the BIFF data to an OLE
     * storage.
     *
     * @param int[] $worksheetSizes The sizes in bytes of the binary worksheet streams
     *
     * @return string Binary data for workbook stream
     */
    public function writeWorkbook(array $worksheetSizes): string
    {
        $this->worksheetSizes = $worksheetSizes;

        // Calculate the number of selected worksheet tabs and call the finalization
        // methods for each worksheet
        $total_worksheets = $this->spreadsheet->getSheetCount();

        // Add part 1 of the Workbook globals, what goes before the SHEET records
        $this->storeBof(0x0005);
        $this->writeCodepage();
        $this->writeWindow1();

        $this->writeDateMode();
        $this->writeAllFonts();
        $this->writeAllNumberFormats();
        $this->writeAllXfs();
        $this->writeAllStyles();
        $this->writePalette();

        // Prepare part 3 of the workbook global stream, what goes after the SHEET records
        $part3 = '';
        if ($this->countryCode !== -1) {
            $part3 .= $this->writeCountry();
        }
        $part3 .= $this->writeRecalcId();

        $part3 .= $this->writeSupbookInternal();
        /* TODO: store external SUPBOOK records and XCT and CRN records
        in case of external references for BIFF8 */
        $part3 .= $this->writeExternalsheetBiff8();
        $part3 .= $this->writeAllDefinedNamesBiff8();
        $part3 .= $this->writeMsoDrawingGroup();
        $part3 .= $this->writeSharedStringsTable();

        $part3 .= $this->writeEof();

        // Add part 2 of the Workbook globals, the SHEET records
        $this->calcSheetOffsets();
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $this->writeBoundSheet($this->spreadsheet->getSheet($i), $this->worksheetOffsets[$i]);
        }

        // Add part 3 of the Workbook globals
        $this->_data .= $part3;

        return $this->_data;
    }

    /**
     * Calculate offsets for Worksheet BOF records.
     */
    private function calcSheetOffsets(): void
    {
        $boundsheet_length = 10; // fixed length for a BOUNDSHEET record

        // size of Workbook globals part 1 + 3
        $offset = $this->_datasize;

        // add size of Workbook globals part 2, the length of the SHEET records
        $total_worksheets = count($this->spreadsheet->getAllSheets());
        foreach ($this->spreadsheet->getWorksheetIterator() as $sheet) {
            $offset += $boundsheet_length + strlen(StringHelper::UTF8toBIFF8UnicodeShort($sheet->getTitle()));
        }

        // add the sizes of each of the Sheet substreams, respectively
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $this->worksheetOffsets[$i] = $offset;
            $offset += $this->worksheetSizes[$i];
        }
        //$this->biffSize = $offset;
    }

    /**
     * Store the Excel FONT records.
     */
    private function writeAllFonts(): void
    {
        foreach ($this->fontWriters as $fontWriter) {
            $this->append($fontWriter->writeFont());
        }
    }

    /**
     * Store user defined numerical formats i.e. FORMAT records.
     */
    private function writeAllNumberFormats(): void
    {
        foreach ($this->numberFormats as $numberFormatIndex => $numberFormat) {
            $this->writeNumberFormat((string) $numberFormat->getFormatCode(), $numberFormatIndex);
        }
    }

    /**
     * Write all XF records.
     */
    private function writeAllXfs(): void
    {
        foreach ($this->xfWriters as $xfWriter) {
            $this->append($xfWriter->writeXf());
        }
    }

    /**
     * Write all STYLE records.
     */
    private function writeAllStyles(): void
    {
        $this->writeStyle();
    }

    private function parseDefinedNameValue(DefinedName $definedName): string
    {
        $definedRange = $definedName->getValue();
        $splitCount = Preg::matchAllWithOffsets(
            '/' . Calculation::CALCULATION_REGEXP_CELLREF . '/mui',
            $definedRange,
            $splitRanges
        );

        $lengths = array_map([StringHelper::class, 'strlenAllowNull'], array_column($splitRanges[0], 0));
        $offsets = array_column($splitRanges[0], 1);

        $worksheets = $splitRanges[2];
        $columns = $splitRanges[6];
        $rows = $splitRanges[7];

        while ($splitCount > 0) {
            --$splitCount;
            $length = $lengths[$splitCount];
            $offset = $offsets[$splitCount];
            $worksheet = $worksheets[$splitCount][0];
            $column = $columns[$splitCount][0];
            $row = $rows[$splitCount][0];

            $newRange = '';
            if (empty($worksheet)) {
                if (($offset === 0) || ($definedRange[$offset - 1] !== ':')) {
                    // We should have a worksheet
                    $worksheet = $definedName->getWorksheet() ? $definedName->getWorksheet()->getTitle() : null;
                }
            } else {
                $worksheet = str_replace("''", "'", trim($worksheet, "'"));
            }
            if (!empty($worksheet)) {
                $newRange = "'" . str_replace("'", "''", $worksheet) . "'!";
            }

            if (!empty($column)) {
                $newRange .= "\${$column}";
            }
            if (!empty($row)) {
                $newRange .= "\${$row}";
            }

            $definedRange = substr($definedRange, 0, $offset) . $newRange . substr($definedRange, $offset + $length);
        }

        return $definedRange;
    }

    /**
     * Writes all the DEFINEDNAME records (BIFF8).
     * So far this is only used for repeating rows/columns (print titles) and print areas.
     */
    private function writeAllDefinedNamesBiff8(): string
    {
        $chunk = '';

        // Named ranges
        $definedNames = $this->spreadsheet->getDefinedNames();
        if (count($definedNames) > 0) {
            // Loop named ranges
            foreach ($definedNames as $definedName) {
                $range = $this->parseDefinedNameValue($definedName);

                // parse formula
                try {
                    $this->parser->parse($range);
                    $formulaData = $this->parser->toReversePolish();

                    // make sure tRef3d is of type tRef3dR (0x3A)
                    if (isset($formulaData[0]) && ($formulaData[0] == "\x7A" || $formulaData[0] == "\x5A")) {
                        $formulaData = "\x3A" . substr($formulaData, 1);
                    }

                    if ($definedName->getLocalOnly()) {
                        // local scope
                        $scopeWs = $definedName->getScope();
                        $scope = ($scopeWs === null) ? 0 : ($this->spreadsheet->getIndex($scopeWs) + 1);
                    } else {
                        // global scope
                        $scope = 0;
                    }
                    $chunk .= $this->writeData($this->writeDefinedNameBiff8($definedName->getName(), $formulaData, $scope, false));
                } catch (PhpSpreadsheetException) {
                    // do nothing
                }
            }
        }

        // total number of sheets
        $total_worksheets = $this->spreadsheet->getSheetCount();

        // write the print titles (repeating rows, columns), if any
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->spreadsheet->getSheet($i)->getPageSetup();
            // simultaneous repeatColumns repeatRows
            if ($sheetSetup->isColumnsToRepeatAtLeftSet() && $sheetSetup->isRowsToRepeatAtTopSet()) {
                $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                $colmin = Coordinate::columnIndexFromString($repeat[0]) - 1;
                $colmax = Coordinate::columnIndexFromString($repeat[1]) - 1;

                $repeat = $sheetSetup->getRowsToRepeatAtTop();
                $rowmin = $repeat[0] - 1;
                $rowmax = $repeat[1] - 1;

                // construct formula data manually
                $formulaData = pack('Cv', 0x29, 0x17); // tMemFunc
                $formulaData .= pack('Cvvvvv', 0x3B, $i, 0, 65535, $colmin, $colmax); // tArea3d
                $formulaData .= pack('Cvvvvv', 0x3B, $i, $rowmin, $rowmax, 0, 255); // tArea3d
                $formulaData .= pack('C', 0x10); // tList

                // store the DEFINEDNAME record
                $chunk .= $this->writeData($this->writeDefinedNameBiff8(pack('C', 0x07), $formulaData, $i + 1, true));
            } elseif ($sheetSetup->isColumnsToRepeatAtLeftSet() || $sheetSetup->isRowsToRepeatAtTopSet()) {
                // (exclusive) either repeatColumns or repeatRows.
                // Columns to repeat
                if ($sheetSetup->isColumnsToRepeatAtLeftSet()) {
                    $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                    $colmin = Coordinate::columnIndexFromString($repeat[0]) - 1;
                    $colmax = Coordinate::columnIndexFromString($repeat[1]) - 1;
                } else {
                    $colmin = 0;
                    $colmax = 255;
                }
                // Rows to repeat
                if ($sheetSetup->isRowsToRepeatAtTopSet()) {
                    $repeat = $sheetSetup->getRowsToRepeatAtTop();
                    $rowmin = $repeat[0] - 1;
                    $rowmax = $repeat[1] - 1;
                } else {
                    $rowmin = 0;
                    $rowmax = 65535;
                }

                // construct formula data manually because parser does not recognize absolute 3d cell references
                $formulaData = pack('Cvvvvv', 0x3B, $i, $rowmin, $rowmax, $colmin, $colmax);

                // store the DEFINEDNAME record
                $chunk .= $this->writeData($this->writeDefinedNameBiff8(pack('C', 0x07), $formulaData, $i + 1, true));
            }
        }

        // write the print areas, if any
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->spreadsheet->getSheet($i)->getPageSetup();
            if ($sheetSetup->isPrintAreaSet()) {
                // Print area, e.g. A3:J6,H1:X20
                $printArea = Coordinate::splitRange($sheetSetup->getPrintArea());
                $countPrintArea = count($printArea);

                $formulaData = '';
                for ($j = 0; $j < $countPrintArea; ++$j) {
                    $printAreaRect = $printArea[$j]; // e.g. A3:J6
                    $printAreaRect[0] = Coordinate::indexesFromString($printAreaRect[0]);
                    /** @var string */
                    $printAreaRect1 = $printAreaRect[1];
                    $printAreaRect[1] = Coordinate::indexesFromString($printAreaRect1);

                    $print_rowmin = $printAreaRect[0][1] - 1;
                    $print_rowmax = $printAreaRect[1][1] - 1;
                    $print_colmin = $printAreaRect[0][0] - 1;
                    $print_colmax = $printAreaRect[1][0] - 1;

                    // construct formula data manually because parser does not recognize absolute 3d cell references
                    $formulaData .= pack('Cvvvvv', 0x3B, $i, $print_rowmin, $print_rowmax, $print_colmin, $print_colmax);

                    if ($j > 0) {
                        $formulaData .= pack('C', 0x10); // list operator token ','
                    }
                }

                // store the DEFINEDNAME record
                $chunk .= $this->writeData($this->writeDefinedNameBiff8(pack('C', 0x06), $formulaData, $i + 1, true));
            }
        }

        // write autofilters, if any
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetAutoFilter = $this->spreadsheet->getSheet($i)->getAutoFilter();
            $autoFilterRange = $sheetAutoFilter->getRange();
            if (!empty($autoFilterRange)) {
                $rangeBounds = Coordinate::rangeBoundaries($autoFilterRange);

                //Autofilter built in name
                $name = pack('C', 0x0D);

                $chunk .= $this->writeData($this->writeShortNameBiff8($name, $i + 1, $rangeBounds, true));
            }
        }

        return $chunk;
    }

    /**
     * Write a DEFINEDNAME record for BIFF8 using explicit binary formula data.
     *
     * @param string $name The name in UTF-8
     * @param string $formulaData The binary formula data
     * @param int $sheetIndex 1-based sheet index the defined name applies to. 0 = global
     * @param bool $isBuiltIn Built-in name?
     *
     * @return string Complete binary record data
     */
    private function writeDefinedNameBiff8(string $name, string $formulaData, int $sheetIndex = 0, bool $isBuiltIn = false): string
    {
        $record = 0x0018;

        // option flags
        $options = $isBuiltIn ? 0x20 : 0x00;

        // length of the name, character count
        $nlen = StringHelper::countCharacters($name);

        // name with stripped length field
        $name = substr(StringHelper::UTF8toBIFF8UnicodeLong($name), 2);

        // size of the formula (in bytes)
        $sz = strlen($formulaData);

        // combine the parts
        $data = pack('vCCvvvCCCC', $options, 0, $nlen, $sz, 0, $sheetIndex, 0, 0, 0, 0)
            . $name . $formulaData;
        $length = strlen($data);

        $header = pack('vv', $record, $length);

        return $header . $data;
    }

    /**
     * Write a short NAME record.
     *
     * @param int $sheetIndex 1-based sheet index the defined name applies to. 0 = global
     * @param int[][] $rangeBounds range boundaries
     *
     * @return string Complete binary record data
     * */
    private function writeShortNameBiff8(string $name, int $sheetIndex, array $rangeBounds, bool $isHidden = false): string
    {
        $record = 0x0018;

        // option flags
        $options = ($isHidden ? 0x21 : 0x00);

        $extra = pack(
            'Cvvvvv',
            0x3B,
            $sheetIndex - 1,
            $rangeBounds[0][1] - 1,
            $rangeBounds[1][1] - 1,
            $rangeBounds[0][0] - 1,
            $rangeBounds[1][0] - 1
        );

        // size of the formula (in bytes)
        $sz = strlen($extra);

        // combine the parts
        $data = pack('vCCvvvCCCCC', $options, 0, 1, $sz, 0, $sheetIndex, 0, 0, 0, 0, 0)
            . $name . $extra;
        $length = strlen($data);

        $header = pack('vv', $record, $length);

        return $header . $data;
    }

    /**
     * Stores the CODEPAGE biff record.
     */
    private function writeCodepage(): void
    {
        $record = 0x0042; // Record identifier
        $length = 0x0002; // Number of bytes to follow
        $cv = $this->codepage; // The code page

        $header = pack('vv', $record, $length);
        $data = pack('v', $cv);

        $this->append($header . $data);
    }

    /**
     * Write Excel BIFF WINDOW1 record.
     */
    private function writeWindow1(): void
    {
        $record = 0x003D; // Record identifier
        $length = 0x0012; // Number of bytes to follow

        $xWn = 0x0000; // Horizontal position of window
        $yWn = 0x0000; // Vertical position of window
        $dxWn = 0x25BC; // Width of window
        $dyWn = 0x1572; // Height of window

        $grbit = 0x0038; // Option flags

        // not supported by PhpSpreadsheet, so there is only one selected sheet, the active
        $ctabsel = 1; // Number of workbook tabs selected

        $wTabRatio = 0x0258; // Tab to scrollbar ratio

        // not supported by PhpSpreadsheet, set to 0
        $itabFirst = 0; // 1st displayed worksheet
        $itabCur = $this->spreadsheet->getActiveSheetIndex(); // Active worksheet

        $header = pack('vv', $record, $length);
        $data = pack('vvvvvvvvv', $xWn, $yWn, $dxWn, $dyWn, $grbit, $itabCur, $itabFirst, $ctabsel, $wTabRatio);
        $this->append($header . $data);
    }

    /**
     * Writes Excel BIFF BOUNDSHEET record.
     *
     * @param int $offset Location of worksheet BOF
     */
    private function writeBoundSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, int $offset): void
    {
        $sheetname = $sheet->getTitle();
        $record = 0x0085; // Record identifier
        $ss = match ($sheet->getSheetState()) {
            \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_VISIBLE => 0x00,
            \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN => 0x01,
            \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_VERYHIDDEN => 0x02,
            default => 0x00,
        };

        // sheet type
        $st = 0x00;

        //$grbit = 0x0000; // Visibility and sheet type

        $data = pack('VCC', $offset, $ss, $st);
        $data .= StringHelper::UTF8toBIFF8UnicodeShort($sheetname);

        $length = strlen($data);
        $header = pack('vv', $record, $length);
        $this->append($header . $data);
    }

    /**
     * Write Internal SUPBOOK record.
     */
    private function writeSupbookInternal(): string
    {
        $record = 0x01AE; // Record identifier
        $length = 0x0004; // Bytes to follow

        $header = pack('vv', $record, $length);
        $data = pack('vv', $this->spreadsheet->getSheetCount(), 0x0401);

        return $this->writeData($header . $data);
    }

    /**
     * Writes the Excel BIFF EXTERNSHEET record. These references are used by
     * formulas.
     */
    private function writeExternalsheetBiff8(): string
    {
        $totalReferences = count($this->parser->references);
        $record = 0x0017; // Record identifier
        $length = 2 + 6 * $totalReferences; // Number of bytes to follow

        //$supbook_index = 0; // FIXME: only using internal SUPBOOK record
        $header = pack('vv', $record, $length);
        $data = pack('v', $totalReferences);
        for ($i = 0; $i < $totalReferences; ++$i) {
            $data .= $this->parser->references[$i];
        }

        return $this->writeData($header . $data);
    }

    /**
     * Write Excel BIFF STYLE records.
     */
    private function writeStyle(): void
    {
        $record = 0x0293; // Record identifier
        $length = 0x0004; // Bytes to follow

        $ixfe = 0x8000; // Index to cell style XF
        $BuiltIn = 0x00; // Built-in style
        $iLevel = 0xFF; // Outline style level

        $header = pack('vv', $record, $length);
        $data = pack('vCC', $ixfe, $BuiltIn, $iLevel);
        $this->append($header . $data);
    }

    /**
     * Writes Excel FORMAT record for non "built-in" numerical formats.
     *
     * @param string $format Custom format string
     * @param int $ifmt Format index code
     */
    private function writeNumberFormat(string $format, int $ifmt): void
    {
        $record = 0x041E; // Record identifier

        $numberFormatString = StringHelper::UTF8toBIFF8UnicodeLong($format);
        $length = 2 + strlen($numberFormatString); // Number of bytes to follow

        $header = pack('vv', $record, $length);
        $data = pack('v', $ifmt) . $numberFormatString;
        $this->append($header . $data);
    }

    /**
     * Write DATEMODE record to indicate the date system in use (1904 or 1900).
     */
    private function writeDateMode(): void
    {
        $record = 0x0022; // Record identifier
        $length = 0x0002; // Bytes to follow

        $f1904 = ($this->spreadsheet->getExcelCalendar() === Date::CALENDAR_MAC_1904)
            ? 1  // Flag for 1904 date system
            : 0; // Flag for 1900 date system

        $header = pack('vv', $record, $length);
        $data = pack('v', $f1904);
        $this->append($header . $data);
    }

    /**
     * Stores the COUNTRY record for localization.
     */
    private function writeCountry(): string
    {
        $record = 0x008C; // Record identifier
        $length = 4; // Number of bytes to follow

        $header = pack('vv', $record, $length);
        // using the same country code always for simplicity
        $data = pack('vv', $this->countryCode, $this->countryCode);

        return $this->writeData($header . $data);
    }

    /**
     * Write the RECALCID record.
     */
    private function writeRecalcId(): string
    {
        $record = 0x01C1; // Record identifier
        $length = 8; // Number of bytes to follow

        $header = pack('vv', $record, $length);

        // by inspection of real Excel files, MS Office Excel 2007 writes this
        $data = pack('VV', 0x000001C1, 0x00001E667);

        return $this->writeData($header . $data);
    }

    /**
     * Stores the PALETTE biff record.
     */
    private function writePalette(): void
    {
        $aref = $this->palette;

        $record = 0x0092; // Record identifier
        $length = 2 + 4 * count($aref); // Number of bytes to follow
        $ccv = count($aref); // Number of RGB values to follow
        $data = ''; // The RGB data

        // Pack the RGB data
        foreach ($aref as $color) {
            foreach ($color as $byte) {
                $data .= pack('C', $byte);
            }
        }

        $header = pack('vvv', $record, $length, $ccv);
        $this->append($header . $data);
    }

    /**
     * Handling of the SST continue blocks is complicated by the need to include an
     * additional continuation byte depending on whether the string is split between
     * blocks or whether it starts at the beginning of the block. (There are also
     * additional complications that will arise later when/if Rich Strings are
     * supported).
     *
     * The Excel documentation says that the SST record should be followed by an
     * EXTSST record. The EXTSST record is a hash table that is used to optimise
     * access to SST. However, despite the documentation it doesn't seem to be
     * required so we will ignore it.
     *
     * @return string Binary data
     */
    private function writeSharedStringsTable(): string
    {
        // maximum size of record data (excluding record header)
        $continue_limit = 8224;

        // initialize array of record data blocks
        $recordDatas = [];

        // start SST record data block with total number of strings, total number of unique strings
        $recordData = pack('VV', $this->stringTotal, $this->stringUnique);

        // loop through all (unique) strings in shared strings table
        foreach (array_keys($this->stringTable) as $string) {
            // here $string is a BIFF8 encoded string

            // length = character count
            $headerinfo = unpack('vlength/Cencoding', $string);

            // currently, this is always 1 = uncompressed
            $encoding = $headerinfo['encoding'] ?? 1;

            // initialize finished writing current $string
            $finished = false;

            while ($finished === false) {
                // normally, there will be only one cycle, but if string cannot immediately be written as is
                // there will be need for more than one cylcle, if string longer than one record data block, there
                // may be need for even more cycles

                if (strlen($recordData) + strlen($string) <= $continue_limit) {
                    // then we can write the string (or remainder of string) without any problems
                    $recordData .= $string;

                    if (strlen($recordData) + strlen($string) == $continue_limit) {
                        // we close the record data block, and initialize a new one
                        $recordDatas[] = $recordData;
                        $recordData = '';
                    }

                    // we are finished writing this string
                    $finished = true;
                } else {
                    // special treatment writing the string (or remainder of the string)
                    // If the string is very long it may need to be written in more than one CONTINUE record.

                    // check how many bytes more there is room for in the current record
                    $space_remaining = $continue_limit - strlen($recordData);

                    // minimum space needed
                    // uncompressed: 2 byte string length length field + 1 byte option flags + 2 byte character
                    // compressed:   2 byte string length length field + 1 byte option flags + 1 byte character
                    $min_space_needed = ($encoding == 1) ? 5 : 4;

                    // We have two cases
                    // 1. space remaining is less than minimum space needed
                    //        here we must waste the space remaining and move to next record data block
                    // 2. space remaining is greater than or equal to minimum space needed
                    //        here we write as much as we can in the current block, then move to next record data block

                    if ($space_remaining < $min_space_needed) {
                        // 1. space remaining is less than minimum space needed.
                        // we close the block, store the block data
                        $recordDatas[] = $recordData;

                        // and start new record data block where we start writing the string
                        $recordData = '';
                    } else {
                        // 2. space remaining is greater than or equal to minimum space needed.
                        // initialize effective remaining space, for Unicode strings this may need to be reduced by 1, see below
                        $effective_space_remaining = $space_remaining;

                        // for uncompressed strings, sometimes effective space remaining is reduced by 1
                        if ($encoding == 1 && (strlen($string) - $space_remaining) % 2 == 1) {
                            --$effective_space_remaining;
                        }

                        // one block fininshed, store the block data
                        $recordData .= substr($string, 0, $effective_space_remaining);

                        $string = substr($string, $effective_space_remaining); // for next cycle in while loop
                        $recordDatas[] = $recordData;

                        // start new record data block with the repeated option flags
                        $recordData = pack('C', $encoding);
                    }
                }
            }
        }

        // Store the last record data block unless it is empty
        // if there was no need for any continue records, this will be the for SST record data block itself
        if ($recordData !== '') {
            $recordDatas[] = $recordData;
        }

        // combine into one chunk with all the blocks SST, CONTINUE,...
        $chunk = '';
        foreach ($recordDatas as $i => $recordData) {
            // first block should have the SST record header, remaing should have CONTINUE header
            $record = ($i == 0) ? 0x00FC : 0x003C;

            $header = pack('vv', $record, strlen($recordData));
            $data = $header . $recordData;

            $chunk .= $this->writeData($data);
        }

        return $chunk;
    }

    /**
     * Writes the MSODRAWINGGROUP record if needed. Possibly split using CONTINUE records.
     */
    private function writeMsoDrawingGroup(): string
    {
        // write the Escher stream if necessary
        if (isset($this->escher)) {
            $writer = new Escher($this->escher);
            $data = $writer->close();

            $record = 0x00EB;
            $length = strlen($data);
            $header = pack('vv', $record, $length);

            return $this->writeData($header . $data);
        }

        return '';
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
}
