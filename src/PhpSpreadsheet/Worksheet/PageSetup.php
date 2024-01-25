<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

/**
 * <code>
 * Paper size taken from Office Open XML Part 4 - Markup Language Reference, page 1988:.
 *
 * 1 = Letter paper (8.5 in. by 11 in.)
 * 2 = Letter small paper (8.5 in. by 11 in.)
 * 3 = Tabloid paper (11 in. by 17 in.)
 * 4 = Ledger paper (17 in. by 11 in.)
 * 5 = Legal paper (8.5 in. by 14 in.)
 * 6 = Statement paper (5.5 in. by 8.5 in.)
 * 7 = Executive paper (7.25 in. by 10.5 in.)
 * 8 = A3 paper (297 mm by 420 mm)
 * 9 = A4 paper (210 mm by 297 mm)
 * 10 = A4 small paper (210 mm by 297 mm)
 * 11 = A5 paper (148 mm by 210 mm)
 * 12 = B4 paper (250 mm by 353 mm)
 * 13 = B5 paper (176 mm by 250 mm)
 * 14 = Folio paper (8.5 in. by 13 in.)
 * 15 = Quarto paper (215 mm by 275 mm)
 * 16 = Standard paper (10 in. by 14 in.)
 * 17 = Standard paper (11 in. by 17 in.)
 * 18 = Note paper (8.5 in. by 11 in.)
 * 19 = #9 envelope (3.875 in. by 8.875 in.)
 * 20 = #10 envelope (4.125 in. by 9.5 in.)
 * 21 = #11 envelope (4.5 in. by 10.375 in.)
 * 22 = #12 envelope (4.75 in. by 11 in.)
 * 23 = #14 envelope (5 in. by 11.5 in.)
 * 24 = C paper (17 in. by 22 in.)
 * 25 = D paper (22 in. by 34 in.)
 * 26 = E paper (34 in. by 44 in.)
 * 27 = DL envelope (110 mm by 220 mm)
 * 28 = C5 envelope (162 mm by 229 mm)
 * 29 = C3 envelope (324 mm by 458 mm)
 * 30 = C4 envelope (229 mm by 324 mm)
 * 31 = C6 envelope (114 mm by 162 mm)
 * 32 = C65 envelope (114 mm by 229 mm)
 * 33 = B4 envelope (250 mm by 353 mm)
 * 34 = B5 envelope (176 mm by 250 mm)
 * 35 = B6 envelope (176 mm by 125 mm)
 * 36 = Italy envelope (110 mm by 230 mm)
 * 37 = Monarch envelope (3.875 in. by 7.5 in.).
 * 38 = 6 3/4 envelope (3.625 in. by 6.5 in.)
 * 39 = US standard fanfold (14.875 in. by 11 in.)
 * 40 = German standard fanfold (8.5 in. by 12 in.)
 * 41 = German legal fanfold (8.5 in. by 13 in.)
 * 42 = ISO B4 (250 mm by 353 mm)
 * 43 = Japanese double postcard (200 mm by 148 mm)
 * 44 = Standard paper (9 in. by 11 in.)
 * 45 = Standard paper (10 in. by 11 in.)
 * 46 = Standard paper (15 in. by 11 in.)
 * 47 = Invite envelope (220 mm by 220 mm)
 * 50 = Letter extra paper (9.275 in. by 12 in.)
 * 51 = Legal extra paper (9.275 in. by 15 in.)
 * 52 = Tabloid extra paper (11.69 in. by 18 in.)
 * 53 = A4 extra paper (236 mm by 322 mm)
 * 54 = Letter transverse paper (8.275 in. by 11 in.)
 * 55 = A4 transverse paper (210 mm by 297 mm)
 * 56 = Letter extra transverse paper (9.275 in. by 12 in.)
 * 57 = SuperA/SuperA/A4 paper (227 mm by 356 mm)
 * 58 = SuperB/SuperB/A3 paper (305 mm by 487 mm)
 * 59 = Letter plus paper (8.5 in. by 12.69 in.)
 * 60 = A4 plus paper (210 mm by 330 mm)
 * 61 = A5 transverse paper (148 mm by 210 mm)
 * 62 = JIS B5 transverse paper (182 mm by 257 mm)
 * 63 = A3 extra paper (322 mm by 445 mm)
 * 64 = A5 extra paper (174 mm by 235 mm)
 * 65 = ISO B5 extra paper (201 mm by 276 mm)
 * 66 = A2 paper (420 mm by 594 mm)
 * 67 = A3 transverse paper (297 mm by 420 mm)
 * 68 = A3 extra transverse paper (322 mm by 445 mm)
 * </code>
 */
class PageSetup
{
    // Paper size
    public const PAPERSIZE_LETTER = 1;
    public const PAPERSIZE_LETTER_SMALL = 2;
    public const PAPERSIZE_TABLOID = 3;
    public const PAPERSIZE_LEDGER = 4;
    public const PAPERSIZE_LEGAL = 5;
    public const PAPERSIZE_STATEMENT = 6;
    public const PAPERSIZE_EXECUTIVE = 7;
    public const PAPERSIZE_A3 = 8;
    public const PAPERSIZE_A4 = 9;
    public const PAPERSIZE_A4_SMALL = 10;
    public const PAPERSIZE_A5 = 11;
    public const PAPERSIZE_B4 = 12;
    public const PAPERSIZE_B5 = 13;
    public const PAPERSIZE_FOLIO = 14;
    public const PAPERSIZE_QUARTO = 15;
    public const PAPERSIZE_STANDARD_1 = 16;
    public const PAPERSIZE_STANDARD_2 = 17;
    public const PAPERSIZE_NOTE = 18;
    public const PAPERSIZE_NO9_ENVELOPE = 19;
    public const PAPERSIZE_NO10_ENVELOPE = 20;
    public const PAPERSIZE_NO11_ENVELOPE = 21;
    public const PAPERSIZE_NO12_ENVELOPE = 22;
    public const PAPERSIZE_NO14_ENVELOPE = 23;
    public const PAPERSIZE_C = 24;
    public const PAPERSIZE_D = 25;
    public const PAPERSIZE_E = 26;
    public const PAPERSIZE_DL_ENVELOPE = 27;
    public const PAPERSIZE_C5_ENVELOPE = 28;
    public const PAPERSIZE_C3_ENVELOPE = 29;
    public const PAPERSIZE_C4_ENVELOPE = 30;
    public const PAPERSIZE_C6_ENVELOPE = 31;
    public const PAPERSIZE_C65_ENVELOPE = 32;
    public const PAPERSIZE_B4_ENVELOPE = 33;
    public const PAPERSIZE_B5_ENVELOPE = 34;
    public const PAPERSIZE_B6_ENVELOPE = 35;
    public const PAPERSIZE_ITALY_ENVELOPE = 36;
    public const PAPERSIZE_MONARCH_ENVELOPE = 37;
    public const PAPERSIZE_6_3_4_ENVELOPE = 38;
    public const PAPERSIZE_US_STANDARD_FANFOLD = 39;
    public const PAPERSIZE_GERMAN_STANDARD_FANFOLD = 40;
    public const PAPERSIZE_GERMAN_LEGAL_FANFOLD = 41;
    public const PAPERSIZE_ISO_B4 = 42;
    public const PAPERSIZE_JAPANESE_DOUBLE_POSTCARD = 43;
    public const PAPERSIZE_STANDARD_PAPER_1 = 44;
    public const PAPERSIZE_STANDARD_PAPER_2 = 45;
    public const PAPERSIZE_STANDARD_PAPER_3 = 46;
    public const PAPERSIZE_INVITE_ENVELOPE = 47;
    public const PAPERSIZE_LETTER_EXTRA_PAPER = 48;
    public const PAPERSIZE_LEGAL_EXTRA_PAPER = 49;
    public const PAPERSIZE_TABLOID_EXTRA_PAPER = 50;
    public const PAPERSIZE_A4_EXTRA_PAPER = 51;
    public const PAPERSIZE_LETTER_TRANSVERSE_PAPER = 52;
    public const PAPERSIZE_A4_TRANSVERSE_PAPER = 53;
    public const PAPERSIZE_LETTER_EXTRA_TRANSVERSE_PAPER = 54;
    public const PAPERSIZE_SUPERA_SUPERA_A4_PAPER = 55;
    public const PAPERSIZE_SUPERB_SUPERB_A3_PAPER = 56;
    public const PAPERSIZE_LETTER_PLUS_PAPER = 57;
    public const PAPERSIZE_A4_PLUS_PAPER = 58;
    public const PAPERSIZE_A5_TRANSVERSE_PAPER = 59;
    public const PAPERSIZE_JIS_B5_TRANSVERSE_PAPER = 60;
    public const PAPERSIZE_A3_EXTRA_PAPER = 61;
    public const PAPERSIZE_A5_EXTRA_PAPER = 62;
    public const PAPERSIZE_ISO_B5_EXTRA_PAPER = 63;
    public const PAPERSIZE_A2_PAPER = 64;
    public const PAPERSIZE_A3_TRANSVERSE_PAPER = 65;
    public const PAPERSIZE_A3_EXTRA_TRANSVERSE_PAPER = 66;

    // Page orientation
    public const ORIENTATION_DEFAULT = 'default';
    public const ORIENTATION_LANDSCAPE = 'landscape';
    public const ORIENTATION_PORTRAIT = 'portrait';

    // Print Range Set Method
    public const SETPRINTRANGE_OVERWRITE = 'O';
    public const SETPRINTRANGE_INSERT = 'I';

    public const PAGEORDER_OVER_THEN_DOWN = 'overThenDown';
    public const PAGEORDER_DOWN_THEN_OVER = 'downThenOver';

    /**
     * Paper size default.
     */
    private static int $paperSizeDefault = self::PAPERSIZE_LETTER;

    /**
     * Paper size.
     */
    private ?int $paperSize = null;

    /**
     * Orientation default.
     */
    private static string $orientationDefault = self::ORIENTATION_DEFAULT;

    /**
     * Orientation.
     */
    private string $orientation;

    /**
     * Scale (Print Scale).
     *
     * Print scaling. Valid values range from 10 to 400
     * This setting is overridden when fitToWidth and/or fitToHeight are in use
     */
    private ?int $scale = 100;

    /**
     * Fit To Page
     * Whether scale or fitToWith / fitToHeight applies.
     */
    private bool $fitToPage = false;

    /**
     * Fit To Height
     * Number of vertical pages to fit on.
     */
    private ?int $fitToHeight = 1;

    /**
     * Fit To Width
     * Number of horizontal pages to fit on.
     */
    private ?int $fitToWidth = 1;

    /**
     * Columns to repeat at left.
     *
     * @var array Containing start column and end column, empty array if option unset
     */
    private array $columnsToRepeatAtLeft = ['', ''];

    /**
     * Rows to repeat at top.
     *
     * @var array Containing start row number and end row number, empty array if option unset
     */
    private array $rowsToRepeatAtTop = [0, 0];

    /**
     * Center page horizontally.
     */
    private bool $horizontalCentered = false;

    /**
     * Center page vertically.
     */
    private bool $verticalCentered = false;

    /**
     * Print area.
     */
    private ?string $printArea = null;

    /**
     * First page number.
     */
    private ?int $firstPageNumber = null;

    private string $pageOrder = self::PAGEORDER_DOWN_THEN_OVER;

    /**
     * Create a new PageSetup.
     */
    public function __construct()
    {
        $this->orientation = self::$orientationDefault;
    }

    /**
     * Get Paper Size.
     */
    public function getPaperSize(): int
    {
        return $this->paperSize ?? self::$paperSizeDefault;
    }

    /**
     * Set Paper Size.
     *
     * @param int $paperSize see self::PAPERSIZE_*
     *
     * @return $this
     */
    public function setPaperSize(int $paperSize): static
    {
        $this->paperSize = $paperSize;

        return $this;
    }

    /**
     * Get Paper Size default.
     */
    public static function getPaperSizeDefault(): int
    {
        return self::$paperSizeDefault;
    }

    /**
     * Set Paper Size Default.
     */
    public static function setPaperSizeDefault(int $paperSize): void
    {
        self::$paperSizeDefault = $paperSize;
    }

    /**
     * Get Orientation.
     */
    public function getOrientation(): string
    {
        return $this->orientation;
    }

    /**
     * Set Orientation.
     *
     * @param string $orientation see self::ORIENTATION_*
     *
     * @return $this
     */
    public function setOrientation(string $orientation): static
    {
        if ($orientation === self::ORIENTATION_LANDSCAPE || $orientation === self::ORIENTATION_PORTRAIT || $orientation === self::ORIENTATION_DEFAULT) {
            $this->orientation = $orientation;
        }

        return $this;
    }

    public static function getOrientationDefault(): string
    {
        return self::$orientationDefault;
    }

    public static function setOrientationDefault(string $orientation): void
    {
        if ($orientation === self::ORIENTATION_LANDSCAPE || $orientation === self::ORIENTATION_PORTRAIT || $orientation === self::ORIENTATION_DEFAULT) {
            self::$orientationDefault = $orientation;
        }
    }

    /**
     * Get Scale.
     */
    public function getScale(): ?int
    {
        return $this->scale;
    }

    /**
     * Set Scale.
     * Print scaling. Valid values range from 10 to 400
     * This setting is overridden when fitToWidth and/or fitToHeight are in use.
     *
     * @param bool $update Update fitToPage so scaling applies rather than fitToHeight / fitToWidth
     *
     * @return $this
     */
    public function setScale(?int $scale, bool $update = true): static
    {
        // Microsoft Office Excel 2007 only allows setting a scale between 10 and 400 via the user interface,
        // but it is apparently still able to handle any scale >= 0, where 0 results in 100
        if ($scale === null || $scale >= 0) {
            $this->scale = $scale;
            if ($update) {
                $this->fitToPage = false;
            }
        } else {
            throw new PhpSpreadsheetException('Scale must not be negative');
        }

        return $this;
    }

    /**
     * Get Fit To Page.
     */
    public function getFitToPage(): bool
    {
        return $this->fitToPage;
    }

    /**
     * Set Fit To Page.
     *
     * @return $this
     */
    public function setFitToPage(bool $fitToPage): static
    {
        $this->fitToPage = $fitToPage;

        return $this;
    }

    /**
     * Get Fit To Height.
     */
    public function getFitToHeight(): ?int
    {
        return $this->fitToHeight;
    }

    /**
     * Set Fit To Height.
     *
     * @param bool $update Update fitToPage so it applies rather than scaling
     *
     * @return $this
     */
    public function setFitToHeight(?int $fitToHeight, bool $update = true): static
    {
        $this->fitToHeight = $fitToHeight;
        if ($update) {
            $this->fitToPage = true;
        }

        return $this;
    }

    /**
     * Get Fit To Width.
     */
    public function getFitToWidth(): ?int
    {
        return $this->fitToWidth;
    }

    /**
     * Set Fit To Width.
     *
     * @param bool $update Update fitToPage so it applies rather than scaling
     *
     * @return $this
     */
    public function setFitToWidth(?int $value, bool $update = true): static
    {
        $this->fitToWidth = $value;
        if ($update) {
            $this->fitToPage = true;
        }

        return $this;
    }

    /**
     * Is Columns to repeat at left set?
     */
    public function isColumnsToRepeatAtLeftSet(): bool
    {
        if (!empty($this->columnsToRepeatAtLeft)) {
            if ($this->columnsToRepeatAtLeft[0] != '' && $this->columnsToRepeatAtLeft[1] != '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Columns to repeat at left.
     *
     * @return array Containing start column and end column, empty array if option unset
     */
    public function getColumnsToRepeatAtLeft(): array
    {
        return $this->columnsToRepeatAtLeft;
    }

    /**
     * Set Columns to repeat at left.
     *
     * @param array $columnsToRepeatAtLeft Containing start column and end column, empty array if option unset
     *
     * @return $this
     */
    public function setColumnsToRepeatAtLeft(array $columnsToRepeatAtLeft): static
    {
        $this->columnsToRepeatAtLeft = $columnsToRepeatAtLeft;

        return $this;
    }

    /**
     * Set Columns to repeat at left by start and end.
     *
     * @param string $start eg: 'A'
     * @param string $end eg: 'B'
     *
     * @return $this
     */
    public function setColumnsToRepeatAtLeftByStartAndEnd(string $start, string $end): static
    {
        $this->columnsToRepeatAtLeft = [$start, $end];

        return $this;
    }

    /**
     * Is Rows to repeat at top set?
     */
    public function isRowsToRepeatAtTopSet(): bool
    {
        if (!empty($this->rowsToRepeatAtTop)) {
            if ($this->rowsToRepeatAtTop[0] != 0 && $this->rowsToRepeatAtTop[1] != 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Rows to repeat at top.
     *
     * @return array Containing start column and end column, empty array if option unset
     */
    public function getRowsToRepeatAtTop(): array
    {
        return $this->rowsToRepeatAtTop;
    }

    /**
     * Set Rows to repeat at top.
     *
     * @param array $rowsToRepeatAtTop Containing start column and end column, empty array if option unset
     *
     * @return $this
     */
    public function setRowsToRepeatAtTop(array $rowsToRepeatAtTop): static
    {
        $this->rowsToRepeatAtTop = $rowsToRepeatAtTop;

        return $this;
    }

    /**
     * Set Rows to repeat at top by start and end.
     *
     * @param int $start eg: 1
     * @param int $end eg: 1
     *
     * @return $this
     */
    public function setRowsToRepeatAtTopByStartAndEnd(int $start, int $end): static
    {
        $this->rowsToRepeatAtTop = [$start, $end];

        return $this;
    }

    /**
     * Get center page horizontally.
     */
    public function getHorizontalCentered(): bool
    {
        return $this->horizontalCentered;
    }

    /**
     * Set center page horizontally.
     *
     * @return $this
     */
    public function setHorizontalCentered(bool $value): static
    {
        $this->horizontalCentered = $value;

        return $this;
    }

    /**
     * Get center page vertically.
     */
    public function getVerticalCentered(): bool
    {
        return $this->verticalCentered;
    }

    /**
     * Set center page vertically.
     *
     * @return $this
     */
    public function setVerticalCentered(bool $value): static
    {
        $this->verticalCentered = $value;

        return $this;
    }

    /**
     * Get print area.
     *
     * @param int $index Identifier for a specific print area range if several ranges have been set
     *                            Default behaviour, or a index value of 0, will return all ranges as a comma-separated string
     *                            Otherwise, the specific range identified by the value of $index will be returned
     *                            Print areas are numbered from 1
     */
    public function getPrintArea(int $index = 0): string
    {
        if ($index == 0) {
            return (string) $this->printArea;
        }
        $printAreas = explode(',', (string) $this->printArea);
        if (isset($printAreas[$index - 1])) {
            return $printAreas[$index - 1];
        }

        throw new PhpSpreadsheetException('Requested Print Area does not exist');
    }

    /**
     * Is print area set?
     *
     * @param int $index Identifier for a specific print area range if several ranges have been set
     *                            Default behaviour, or an index value of 0, will identify whether any print range is set
     *                            Otherwise, existence of the range identified by the value of $index will be returned
     *                            Print areas are numbered from 1
     */
    public function isPrintAreaSet(int $index = 0): bool
    {
        if ($index == 0) {
            return $this->printArea !== null;
        }
        $printAreas = explode(',', (string) $this->printArea);

        return isset($printAreas[$index - 1]);
    }

    /**
     * Clear a print area.
     *
     * @param int $index Identifier for a specific print area range if several ranges have been set
     *                            Default behaviour, or an index value of 0, will clear all print ranges that are set
     *                            Otherwise, the range identified by the value of $index will be removed from the series
     *                            Print areas are numbered from 1
     *
     * @return $this
     */
    public function clearPrintArea(int $index = 0): static
    {
        if ($index == 0) {
            $this->printArea = null;
        } else {
            $printAreas = explode(',', (string) $this->printArea);
            if (isset($printAreas[$index - 1])) {
                unset($printAreas[$index - 1]);
                $this->printArea = implode(',', $printAreas);
            }
        }

        return $this;
    }

    /**
     * Set print area. e.g. 'A1:D10' or 'A1:D10,G5:M20'.
     *
     * @param int $index Identifier for a specific print area range allowing several ranges to be set
     *                            When the method is "O"verwrite, then a positive integer index will overwrite that indexed
     *                                entry in the print areas list; a negative index value will identify which entry to
     *                                overwrite working bacward through the print area to the list, with the last entry as -1.
     *                                Specifying an index value of 0, will overwrite <b>all</b> existing print ranges.
     *                            When the method is "I"nsert, then a positive index will insert after that indexed entry in
     *                                the print areas list, while a negative index will insert before the indexed entry.
     *                                Specifying an index value of 0, will always append the new print range at the end of the
     *                                list.
     *                            Print areas are numbered from 1
     * @param string $method Determines the method used when setting multiple print areas
     *                            Default behaviour, or the "O" method, overwrites existing print area
     *                            The "I" method, inserts the new print area before any specified index, or at the end of the list
     *
     * @return $this
     */
    public function setPrintArea(string $value, int $index = 0, string $method = self::SETPRINTRANGE_OVERWRITE): static
    {
        if (str_contains($value, '!')) {
            throw new PhpSpreadsheetException('Cell coordinate must not specify a worksheet.');
        } elseif (!str_contains($value, ':')) {
            throw new PhpSpreadsheetException('Cell coordinate must be a range of cells.');
        } elseif (str_contains($value, '$')) {
            throw new PhpSpreadsheetException('Cell coordinate must not be absolute.');
        }
        $value = strtoupper($value);
        if (!$this->printArea) {
            $index = 0;
        }

        if ($method == self::SETPRINTRANGE_OVERWRITE) {
            if ($index == 0) {
                $this->printArea = $value;
            } else {
                $printAreas = explode(',', (string) $this->printArea);
                if ($index < 0) {
                    $index = count($printAreas) - abs($index) + 1;
                }
                if (($index <= 0) || ($index > count($printAreas))) {
                    throw new PhpSpreadsheetException('Invalid index for setting print range.');
                }
                $printAreas[$index - 1] = $value;
                $this->printArea = implode(',', $printAreas);
            }
        } elseif ($method == self::SETPRINTRANGE_INSERT) {
            if ($index == 0) {
                $this->printArea = $this->printArea ? ($this->printArea . ',' . $value) : $value;
            } else {
                $printAreas = explode(',', (string) $this->printArea);
                if ($index < 0) {
                    $index = (int) abs($index) - 1;
                }
                if ($index > count($printAreas)) {
                    throw new PhpSpreadsheetException('Invalid index for setting print range.');
                }
                $printAreas = array_merge(array_slice($printAreas, 0, $index), [$value], array_slice($printAreas, $index));
                $this->printArea = implode(',', $printAreas);
            }
        } else {
            throw new PhpSpreadsheetException('Invalid method for setting print range.');
        }

        return $this;
    }

    /**
     * Add a new print area (e.g. 'A1:D10' or 'A1:D10,G5:M20') to the list of print areas.
     *
     * @param int $index Identifier for a specific print area range allowing several ranges to be set
     *                            A positive index will insert after that indexed entry in the print areas list, while a
     *                                negative index will insert before the indexed entry.
     *                                Specifying an index value of 0, will always append the new print range at the end of the
     *                                list.
     *                            Print areas are numbered from 1
     *
     * @return $this
     */
    public function addPrintArea(string $value, int $index = -1): static
    {
        return $this->setPrintArea($value, $index, self::SETPRINTRANGE_INSERT);
    }

    /**
     * Set print area.
     *
     * @param int $column1 Column 1
     * @param int $row1 Row 1
     * @param int $column2 Column 2
     * @param int $row2 Row 2
     * @param int $index Identifier for a specific print area range allowing several ranges to be set
     *                                When the method is "O"verwrite, then a positive integer index will overwrite that indexed
     *                                    entry in the print areas list; a negative index value will identify which entry to
     *                                    overwrite working backward through the print area to the list, with the last entry as -1.
     *                                    Specifying an index value of 0, will overwrite <b>all</b> existing print ranges.
     *                                When the method is "I"nsert, then a positive index will insert after that indexed entry in
     *                                    the print areas list, while a negative index will insert before the indexed entry.
     *                                    Specifying an index value of 0, will always append the new print range at the end of the
     *                                    list.
     *                                Print areas are numbered from 1
     * @param string $method Determines the method used when setting multiple print areas
     *                                Default behaviour, or the "O" method, overwrites existing print area
     *                                The "I" method, inserts the new print area before any specified index, or at the end of the list
     *
     * @return $this
     */
    public function setPrintAreaByColumnAndRow(int $column1, int $row1, int $column2, int $row2, int $index = 0, string $method = self::SETPRINTRANGE_OVERWRITE): static
    {
        return $this->setPrintArea(
            Coordinate::stringFromColumnIndex($column1) . $row1 . ':' . Coordinate::stringFromColumnIndex($column2) . $row2,
            $index,
            $method
        );
    }

    /**
     * Add a new print area to the list of print areas.
     *
     * @param int $column1 Start Column for the print area
     * @param int $row1 Start Row for the print area
     * @param int $column2 End Column for the print area
     * @param int $row2 End Row for the print area
     * @param int $index Identifier for a specific print area range allowing several ranges to be set
     *                                A positive index will insert after that indexed entry in the print areas list, while a
     *                                    negative index will insert before the indexed entry.
     *                                    Specifying an index value of 0, will always append the new print range at the end of the
     *                                    list.
     *                                Print areas are numbered from 1
     *
     * @return $this
     */
    public function addPrintAreaByColumnAndRow(int $column1, int $row1, int $column2, int $row2, int $index = -1): static
    {
        return $this->setPrintArea(
            Coordinate::stringFromColumnIndex($column1) . $row1 . ':' . Coordinate::stringFromColumnIndex($column2) . $row2,
            $index,
            self::SETPRINTRANGE_INSERT
        );
    }

    /**
     * Get first page number.
     */
    public function getFirstPageNumber(): ?int
    {
        return $this->firstPageNumber;
    }

    /**
     * Set first page number.
     *
     * @return $this
     */
    public function setFirstPageNumber(?int $value): static
    {
        $this->firstPageNumber = $value;

        return $this;
    }

    /**
     * Reset first page number.
     *
     * @return $this
     */
    public function resetFirstPageNumber(): static
    {
        return $this->setFirstPageNumber(null);
    }

    public function getPageOrder(): string
    {
        return $this->pageOrder;
    }

    public function setPageOrder(?string $pageOrder): self
    {
        if ($pageOrder === null || $pageOrder === self::PAGEORDER_DOWN_THEN_OVER || $pageOrder === self::PAGEORDER_OVER_THEN_DOWN) {
            $this->pageOrder = $pageOrder ?? self::PAGEORDER_DOWN_THEN_OVER;
        }

        return $this;
    }
}
