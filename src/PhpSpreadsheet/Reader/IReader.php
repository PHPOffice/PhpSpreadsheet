<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface IReader
{
    /**
     * Flag used to load the charts.
     *
     * This flag is supported only for some formats.
     */
    public const LOAD_WITH_CHARTS = 1;

    /**
     * Flag used to read data only, not style or structure information.
     */
    public const READ_DATA_ONLY = 2;

    /**
     * Flag used to ignore empty cells when reading.
     *
     * The ignored cells will not be instantiated.
     */
    public const IGNORE_EMPTY_CELLS = 4;

    /**
     * Flag used to ignore rows without cells.
     *
     * This flag is supported only for some formats.
     * This can heavily improve performance for some files.
     */
    public const IGNORE_ROWS_WITH_NO_CELLS = 8;

    /**
     * Allow external images. Use with caution.
     * Improper specification of these within a spreadsheet
     * can subject the caller to security exploits.
     */
    public const ALLOW_EXTERNAL_IMAGES = 16;
    public const DONT_ALLOW_EXTERNAL_IMAGES = 32;

    public function __construct();

    /**
     * Can the current IReader read the file?
     */
    public function canRead(string $filename): bool;

    /**
     * Read data only?
     *        If this is true, then the Reader will only read data values for cells, it will not read any formatting
     *           or structural information (like merges).
     *        If false (the default) it will read data and formatting.
     */
    public function getReadDataOnly(): bool;

    /**
     * Set read data only
     *        Set to true, to advise the Reader only to read data values for cells, and to ignore any formatting
     *            or structural information (like merges).
     *        Set to false (the default) to advise the Reader to read both data and formatting for cells.
     *
     * @return $this
     */
    public function setReadDataOnly(bool $readDataOnly): self;

    /**
     * Read empty cells?
     *        If this is true (the default), then the Reader will read data values for all cells, irrespective of value.
     *        If false it will not read data for cells containing a null value or an empty string.
     */
    public function getReadEmptyCells(): bool;

    /**
     * Set read empty cells
     *        Set to true (the default) to advise the Reader read data values for all cells, irrespective of value.
     *        Set to false to advise the Reader to ignore cells containing a null value or an empty string.
     *
     * @return $this
     */
    public function setReadEmptyCells(bool $readEmptyCells): self;

    /**
     * Read charts in workbook?
     *      If this is true, then the Reader will include any charts that exist in the workbook.
     *         Note that a ReadDataOnly value of false overrides, and charts won't be read regardless of the IncludeCharts value.
     *      If false (the default) it will ignore any charts defined in the workbook file.
     */
    public function getIncludeCharts(): bool;

    /**
     * Set read charts in workbook
     *     Set to true, to advise the Reader to include any charts that exist in the workbook.
     *         Note that a ReadDataOnly value of false overrides, and charts won't be read regardless of the IncludeCharts value.
     *     Set to false (the default) to discard charts.
     *
     * @return $this
     */
    public function setIncludeCharts(bool $includeCharts): self;

    /**
     * Get which sheets to load
     * Returns either an array of worksheet names (the list of worksheets that should be loaded), or a null
     *        indicating that all worksheets in the workbook should be loaded.
     *
     * @return null|string[]
     */
    public function getLoadSheetsOnly(): ?array;

    /**
     * Set which sheets to load.
     *
     * @param null|string|string[] $value This should be either an array of worksheet names to be loaded,
     *          or a string containing a single worksheet name. If NULL, then it tells the Reader to
     *          read all worksheets in the workbook
     *
     * @return $this
     */
    public function setLoadSheetsOnly(string|array|null $value): self;

    /**
     * Set all sheets to load
     *        Tells the Reader to load all worksheets from the workbook.
     *
     * @return $this
     */
    public function setLoadAllSheets(): self;

    /**
     * Read filter.
     */
    public function getReadFilter(): IReadFilter;

    /**
     * Set read filter.
     *
     * @return $this
     */
    public function setReadFilter(IReadFilter $readFilter): self;

    /**
     * Allow external images. Use with caution.
     * Improper specification of these within a spreadsheet
     * can subject the caller to security exploits.
     */
    public function setAllowExternalImages(bool $allowExternalImages): self;

    public function getAllowExternalImages(): bool;

    /**
     * Loads PhpSpreadsheet from file.
     *
     * @param string $filename The name of the file to load
     * @param int $flags Flags that can change the behaviour of the Writer:
     *            self::LOAD_WITH_CHARTS    Load any charts that are defined (if the Reader supports Charts)
     *            self::READ_DATA_ONLY      Read only data, not style or structure information, from the file
     *            self::IGNORE_EMPTY_CELLS  Don't read empty cells (cells that contain a null value,
     *                                      empty string, or a string containing only whitespace characters)
     *            self::IGNORE_ROWS_WITH_NO_CELLS    Don't load any rows that contain no cells.
     *            self::ALLOW_EXTERNAL_IMAGES    Attempt to fetch images stored outside the spreadsheet.
     *            self::DONT_ALLOW_EXTERNAL_IMAGES    Don't attempt to fetch images stored outside the spreadsheet.
     */
    public function load(string $filename, int $flags = 0): Spreadsheet;
}
