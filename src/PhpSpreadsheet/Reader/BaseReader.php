<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Shared\File;

abstract class BaseReader implements IReader
{
    /**
     * Read data only?
     * Identifies whether the Reader should only read data values for cells, and ignore any formatting information;
     *        or whether it should read both data and formatting.
     *
     * @var bool
     */
    protected $readDataOnly = false;

    /**
     * Read empty cells?
     * Identifies whether the Reader should read data values for cells all cells, or should ignore cells containing
     *         null value or empty string.
     *
     * @var bool
     */
    protected $readEmptyCells = true;

    /**
     * Read charts that are defined in the workbook?
     * Identifies whether the Reader should read the definitions for any charts that exist in the workbook;.
     *
     * @var bool
     */
    protected $includeCharts = false;

    /**
     * Restrict which sheets should be loaded?
     * This property holds an array of worksheet names to be loaded. If null, then all worksheets will be loaded.
     *
     * @var array of string
     */
    protected $loadSheetsOnly;

    /**
     * IReadFilter instance.
     *
     * @var IReadFilter
     */
    protected $readFilter;

    protected $fileHandle;

    /**
     * @var XmlScanner
     */
    protected $securityScanner;

    /**
     * Read data only?
     *        If this is true, then the Reader will only read data values for cells, it will not read any formatting information.
     *        If false (the default) it will read data and formatting.
     *
     * @return bool
     */
    public function getReadDataOnly()
    {
        return $this->readDataOnly;
    }

    /**
     * Set read data only
     *        Set to true, to advise the Reader only to read data values for cells, and to ignore any formatting information.
     *        Set to false (the default) to advise the Reader to read both data and formatting for cells.
     *
     * @param bool $pValue
     *
     * @return IReader
     */
    public function setReadDataOnly($pValue)
    {
        $this->readDataOnly = (bool) $pValue;

        return $this;
    }

    /**
     * Read empty cells?
     *        If this is true (the default), then the Reader will read data values for all cells, irrespective of value.
     *        If false it will not read data for cells containing a null value or an empty string.
     *
     * @return bool
     */
    public function getReadEmptyCells()
    {
        return $this->readEmptyCells;
    }

    /**
     * Set read empty cells
     *        Set to true (the default) to advise the Reader read data values for all cells, irrespective of value.
     *        Set to false to advise the Reader to ignore cells containing a null value or an empty string.
     *
     * @param bool $pValue
     *
     * @return IReader
     */
    public function setReadEmptyCells($pValue)
    {
        $this->readEmptyCells = (bool) $pValue;

        return $this;
    }

    /**
     * Read charts in workbook?
     *        If this is true, then the Reader will include any charts that exist in the workbook.
     *      Note that a ReadDataOnly value of false overrides, and charts won't be read regardless of the IncludeCharts value.
     *        If false (the default) it will ignore any charts defined in the workbook file.
     *
     * @return bool
     */
    public function getIncludeCharts()
    {
        return $this->includeCharts;
    }

    /**
     * Set read charts in workbook
     *        Set to true, to advise the Reader to include any charts that exist in the workbook.
     *      Note that a ReadDataOnly value of false overrides, and charts won't be read regardless of the IncludeCharts value.
     *        Set to false (the default) to discard charts.
     *
     * @param bool $pValue
     *
     * @return IReader
     */
    public function setIncludeCharts($pValue)
    {
        $this->includeCharts = (bool) $pValue;

        return $this;
    }

    /**
     * Get which sheets to load
     * Returns either an array of worksheet names (the list of worksheets that should be loaded), or a null
     *        indicating that all worksheets in the workbook should be loaded.
     *
     * @return mixed
     */
    public function getLoadSheetsOnly()
    {
        return $this->loadSheetsOnly;
    }

    /**
     * Set which sheets to load.
     *
     * @param mixed $value
     *        This should be either an array of worksheet names to be loaded, or a string containing a single worksheet name.
     *        If NULL, then it tells the Reader to read all worksheets in the workbook
     *
     * @return IReader
     */
    public function setLoadSheetsOnly($value)
    {
        if ($value === null) {
            return $this->setLoadAllSheets();
        }

        $this->loadSheetsOnly = is_array($value) ? $value : [$value];

        return $this;
    }

    /**
     * Set all sheets to load
     *        Tells the Reader to load all worksheets from the workbook.
     *
     * @return IReader
     */
    public function setLoadAllSheets()
    {
        $this->loadSheetsOnly = null;

        return $this;
    }

    /**
     * Read filter.
     *
     * @return IReadFilter
     */
    public function getReadFilter()
    {
        return $this->readFilter;
    }

    /**
     * Set read filter.
     *
     * @param IReadFilter $pValue
     *
     * @return IReader
     */
    public function setReadFilter(IReadFilter $pValue)
    {
        $this->readFilter = $pValue;

        return $this;
    }

    public function getSecuritySCanner()
    {
        if (property_exists($this, 'securityScanner')) {
            return $this->securityScanner;
        }

        return null;
    }

    /**
     * Open file for reading.
     *
     * @param string $pFilename
     *
     * @throws Exception
     */
    protected function openFile($pFilename)
    {
        File::assertFile($pFilename);

        // Open file
        $this->fileHandle = fopen($pFilename, 'r');
        if ($this->fileHandle === false) {
            throw new Exception('Could not open file ' . $pFilename . ' for reading.');
        }
    }
}
