<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface IReader
{
    public const IGNORE_EMPTY_CELLS = 1;
    public const READ_DATA_ONLY = 2;
    public const LOAD_WITH_CHARTS = 4;

    /**
     * Can the current IReader read the file?
     */
    public function canRead(string $filename): bool;

    /**
     * Read data only?
     *        If this is true, then the Reader will only read data values for cells, it will not read any formatting information.
     *        If false (the default) it will read data and formatting.
     */
    public function getReadDataOnly(): bool;

    /**
     * Set read data only
     *        Set to true, to advise the Reader only to read data values for cells, and to ignore any formatting information.
     *        Set to false (the default) to advise the Reader to read both data and formatting for cells.
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
     */
    public function setReadEmptyCells(bool $readEmptyCells): self;

    /**
     * Read charts in workbook?
     *        If this is true, then the Reader will include any charts that exist in the workbook.
     *      Note that a ReadDataOnly value of false overrides, and charts won't be read regardless of the IncludeCharts value.
     *        If false (the default) it will ignore any charts defined in the workbook file.
     */
    public function getIncludeCharts(): bool;

    /**
     * Set read charts in workbook
     *        Set to true, to advise the Reader to include any charts that exist in the workbook.
     *      Note that a ReadDataOnly value of false overrides, and charts won't be read regardless of the IncludeCharts value.
     *        Set to false (the default) to discard charts.
     */
    public function setIncludeCharts(bool $includeCharts): self;

    /**
     * Get which sheets to load
     * Returns either an array of worksheet names (the list of worksheets that should be loaded), or a null
     *        indicating that all worksheets in the workbook should be loaded.
     *
     * @return ?string[]
     */
    public function getLoadSheetsOnly();

    /**
     * Set which sheets to load.
     *
     * @param null|string|string[] $sheetList
     *        This should be either an array of worksheet names to be loaded, or a string containing a single worksheet name.
     *        If NULL, then it tells the Reader to read all worksheets in the workbook
     */
    public function setLoadSheetsOnly($sheetList): self;

    /**
     * Set all sheets to load
     *        Tells the Reader to load all worksheets from the workbook.
     */
    public function setLoadAllSheets(): self;

    /**
     * Read filter.
     */
    public function getReadFilter(): IReadFilter;

    /**
     * Set read filter.
     */
    public function setReadFilter(IReadFilter $readFilter): self;

    /**
     * Loads PhpSpreadsheet from file.
     */
    public function load(string $filename, int $flags = 0): Spreadsheet;
}
