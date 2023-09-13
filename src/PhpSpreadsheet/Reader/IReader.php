<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface IReader
{
    public const LOAD_WITH_CHARTS = 1;

    public const READ_DATA_ONLY = 2;

    public const SKIP_EMPTY_CELLS = 4;
    public const IGNORE_EMPTY_CELLS = 4;

    /**
     * IReader constructor.
     */
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
     *
     * @return bool
     */
    public function getReadDataOnly();

    /**
     * Set read data only
     *        Set to true, to advise the Reader only to read data values for cells, and to ignore any formatting
     *            or structural information (like merges).
     *        Set to false (the default) to advise the Reader to read both data and formatting for cells.
     *
     * @param bool $readDataOnly
     *
     * @return IReader
     */
    public function setReadDataOnly($readDataOnly);

    /**
     * Read empty cells?
     *        If this is true (the default), then the Reader will read data values for all cells, irrespective of value.
     *        If false it will not read data for cells containing a null value or an empty string.
     *
     * @return bool
     */
    public function getReadEmptyCells();

    /**
     * Set read empty cells
     *        Set to true (the default) to advise the Reader read data values for all cells, irrespective of value.
     *        Set to false to advise the Reader to ignore cells containing a null value or an empty string.
     *
     * @param bool $readEmptyCells
     *
     * @return IReader
     */
    public function setReadEmptyCells($readEmptyCells);

    /**
     * Read charts in workbook?
     *      If this is true, then the Reader will include any charts that exist in the workbook.
     *         Note that a ReadDataOnly value of false overrides, and charts won't be read regardless of the IncludeCharts value.
     *      If false (the default) it will ignore any charts defined in the workbook file.
     *
     * @return bool
     */
    public function getIncludeCharts();

    /**
     * Set read charts in workbook
     *     Set to true, to advise the Reader to include any charts that exist in the workbook.
     *         Note that a ReadDataOnly value of false overrides, and charts won't be read regardless of the IncludeCharts value.
     *     Set to false (the default) to discard charts.
     *
     * @param bool $includeCharts
     *
     * @return IReader
     */
    public function setIncludeCharts($includeCharts);

    /**
     * Get which sheets to load
     * Returns either an array of worksheet names (the list of worksheets that should be loaded), or a null
     *        indicating that all worksheets in the workbook should be loaded.
     *
     * @return mixed
     */
    public function getLoadSheetsOnly();

    /**
     * Set which sheets to load.
     *
     * @param mixed $value
     *        This should be either an array of worksheet names to be loaded, or a string containing a single worksheet name.
     *        If NULL, then it tells the Reader to read all worksheets in the workbook
     *
     * @return IReader
     */
    public function setLoadSheetsOnly($value);

    /**
     * Set all sheets to load
     *        Tells the Reader to load all worksheets from the workbook.
     *
     * @return IReader
     */
    public function setLoadAllSheets();

    /**
     * Read filter.
     *
     * @return IReadFilter
     */
    public function getReadFilter();

    /**
     * Set read filter.
     *
     * @return IReader
     */
    public function setReadFilter(IReadFilter $readFilter);

    /**
     * Loads PhpSpreadsheet from file.
     *
     * @param string $filename The name of the file to load
     * @param int $flags Flags that can change the behaviour of the Writer:
     *            self::LOAD_WITH_CHARTS    Load any charts that are defined (if the Reader supports Charts)
     *            self::READ_DATA_ONLY      Read only data, not style or structure information, from the file
     *            self::SKIP_EMPTY_CELLS    Don't read empty cells (cells that contain a null value,
     *                                      empty string, or a string containing only whitespace characters)
     *
     * @return Spreadsheet
     */
    public function load(string $filename, int $flags = 0);
}
