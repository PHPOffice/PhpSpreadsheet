<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
     * @var ?string[]
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

    public function getReadDataOnly(): bool
    {
        return $this->readDataOnly;
    }

    public function setReadDataOnly(bool $readDataOnly): IReader
    {
        $this->readDataOnly = (bool) $readDataOnly;

        return $this;
    }

    public function getReadEmptyCells(): bool
    {
        return $this->readEmptyCells;
    }

    public function setReadEmptyCells(bool $readEmptyCells): IReader
    {
        $this->readEmptyCells = $readEmptyCells;

        return $this;
    }

    public function getIncludeCharts(): bool
    {
        return $this->includeCharts;
    }

    public function setIncludeCharts(bool $includeCharts): IReader
    {
        $this->includeCharts = $includeCharts;

        return $this;
    }

    public function getLoadSheetsOnly()
    {
        return $this->loadSheetsOnly;
    }

    public function setLoadSheetsOnly($sheetList): IReader
    {
        if ($sheetList === null) {
            return $this->setLoadAllSheets();
        }

        $this->loadSheetsOnly = is_array($sheetList) ? $sheetList : [$sheetList];

        return $this;
    }

    public function setLoadAllSheets(): IReader
    {
        $this->loadSheetsOnly = null;

        return $this;
    }

    public function getReadFilter(): IReadFilter
    {
        return $this->readFilter;
    }

    public function setReadFilter(IReadFilter $readFilter): IReader
    {
        $this->readFilter = $readFilter;

        return $this;
    }

    public function getSecurityScanner()
    {
        return $this->securityScanner;
    }

    public function setFlags(int $flags): void
    {
        if (((bool) ($flags & self::IGNORE_EMPTY_CELLS)) === true) {
            $this->setReadEmptyCells(false);
        }
        if (((bool) ($flags & self::READ_DATA_ONLY)) === true) {
            $this->setReadDataOnly(true);
        }
        if (((bool) ($flags & self::LOAD_WITH_CHARTS)) === true) {
            $this->setIncludeCharts(true);
        }
    }

    protected function loadSpreadsheetFromFile(string $filename): Spreadsheet
    {
        throw new PhpSpreadsheetException('Reader classes must implement their own loadSpreadsheetFromFile() method');
    }

    /**
     * Loads Spreadsheet from file.
     *
     * @param ?int $flags the optional second parameter flags may be used to identify specific elements
     *                       that should be loaded, but which won't be loaded by default, using these values:
     *                            IReader::IGNORE_EMPTY_CELLS - Don't create empty cells (those containing a null or an empty string)
     *                            IReader::READ_DATA_ONLY - Only read data from the file, not structure or styling
     *                            IReader::LOAD_WITH_CHARTS - Include any charts that are defined in the loaded file
     */
    public function load(string $filename, ?int $flags = null): Spreadsheet
    {
        if ($flags !== null) {
            $this->setFlags($flags);
        }

        IOFactory::setLoading(true);

        try {
            return $this->loadSpreadsheetFromFile($filename);
        } catch (ReaderException $e) {
            throw $e;
        } finally {
            IOFactory::setLoading(false);
        }
    }

    /**
     * Open file for reading.
     */
    protected function openFile(string $filename): void
    {
        $fileHandle = false;
        if ($filename) {
            File::assertFile($filename);

            // Open file
            $fileHandle = fopen($filename, 'rb');
        }
        if ($fileHandle === false) {
            throw new ReaderException("Could not open file {$filename} for reading.");
        }

        $this->fileHandle = $fileHandle;
    }
}
