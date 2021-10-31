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

    public function setReadDataOnly($readCellValuesOnly)
    {
        $this->readDataOnly = (bool) $readCellValuesOnly;

        return $this;
    }

    public function getReadEmptyCells()
    {
        return $this->readEmptyCells;
    }

    public function setReadEmptyCells($readEmptyCells)
    {
        $this->readEmptyCells = (bool) $readEmptyCells;

        return $this;
    }

    public function getIncludeCharts()
    {
        return $this->includeCharts;
    }

    public function setIncludeCharts($includeCharts)
    {
        $this->includeCharts = (bool) $includeCharts;

        return $this;
    }

    public function getLoadSheetsOnly()
    {
        return $this->loadSheetsOnly;
    }

    public function setLoadSheetsOnly($sheetList)
    {
        if ($sheetList === null) {
            return $this->setLoadAllSheets();
        }

        $this->loadSheetsOnly = is_array($sheetList) ? $sheetList : [$sheetList];

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

    public function setReadFilter(IReadFilter $readFilter)
    {
        $this->readFilter = $readFilter;

        return $this;
    }

    public function getSecurityScanner()
    {
        return $this->securityScanner;
    }

    protected function processFlags(int $flags): void
    {
        if (((bool) ($flags & self::LOAD_WITH_CHARTS)) === true) {
            $this->setIncludeCharts(true);
        }
    }

    /**
     * Open file for reading.
     *
     * @param string $filename
     */
    protected function openFile($filename): void
    {
        if ($filename) {
            File::assertFile($filename);

            // Open file
            $fileHandle = fopen($filename, 'rb');
        } else {
            $fileHandle = false;
        }
        if ($fileHandle !== false) {
            $this->fileHandle = $fileHandle;
        } else {
            throw new ReaderException('Could not open file ' . $filename . ' for reading.');
        }
    }
}
