<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
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
     * @var null|string[]
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

    public function __construct()
    {
        $this->readFilter = new DefaultReadFilter();
    }

    public function getReadDataOnly()
    {
        return $this->readDataOnly;
    }

    public function setReadDataOnly($pValue)
    {
        $this->readDataOnly = (bool) $pValue;

        return $this;
    }

    public function getReadEmptyCells()
    {
        return $this->readEmptyCells;
    }

    public function setReadEmptyCells($pValue)
    {
        $this->readEmptyCells = (bool) $pValue;

        return $this;
    }

    public function getIncludeCharts()
    {
        return $this->includeCharts;
    }

    public function setIncludeCharts($pValue)
    {
        $this->includeCharts = (bool) $pValue;

        return $this;
    }

    public function getLoadSheetsOnly()
    {
        return $this->loadSheetsOnly;
    }

    public function setLoadSheetsOnly($value)
    {
        if ($value === null) {
            return $this->setLoadAllSheets();
        }

        $this->loadSheetsOnly = is_array($value) ? $value : [$value];

        return $this;
    }

    public function setLoadAllSheets()
    {
        $this->loadSheetsOnly = null;

        return $this;
    }

    public function getReadFilter()
    {
        return $this->readFilter;
    }

    public function setReadFilter(IReadFilter $pValue)
    {
        $this->readFilter = $pValue;

        return $this;
    }

    public function getSecurityScanner()
    {
        return $this->securityScanner;
    }

    /**
     * Open file for reading.
     *
     * @param string $pFilename
     */
    protected function openFile($pFilename): void
    {
        if ($pFilename) {
            File::assertFile($pFilename);

            // Open file
            $fileHandle = fopen($pFilename, 'rb');
        } else {
            $fileHandle = false;
        }
        if ($fileHandle !== false) {
            $this->fileHandle = $fileHandle;
        } else {
            throw new ReaderException('Could not open file ' . $pFilename . ' for reading.');
        }
    }
}
