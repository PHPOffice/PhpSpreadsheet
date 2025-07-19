<?php

namespace PhpOffice\PhpSpreadsheet\Reader;

use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
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
     */
    protected bool $readDataOnly = false;

    /**
     * Read empty cells?
     * Identifies whether the Reader should read data values for all cells, or should ignore cells containing
     *         null value or empty string.
     */
    protected bool $readEmptyCells = true;

    /**
     * Read charts that are defined in the workbook?
     * Identifies whether the Reader should read the definitions for any charts that exist in the workbook;.
     */
    protected bool $includeCharts = false;

    /**
     * Restrict which sheets should be loaded?
     * This property holds an array of worksheet names to be loaded. If null, then all worksheets will be loaded.
     * This property is ignored for Csv, Html, and Slk.
     *
     * @var null|string[]
     */
    protected ?array $loadSheetsOnly = null;

    /**
     * Ignore rows with no cells?
     * Identifies whether the Reader should ignore rows with no cells.
     *        Currently implemented only for Xlsx.
     */
    protected bool $ignoreRowsWithNoCells = false;

    /**
     * Allow external images. Use with caution.
     * Improper specification of these within a spreadsheet
     * can subject the caller to security exploits.
     */
    protected bool $allowExternalImages = true;

    /**
     * IReadFilter instance.
     */
    protected IReadFilter $readFilter;

    /** @var resource */
    protected $fileHandle;

    protected ?XmlScanner $securityScanner = null;

    protected ?IValueBinder $valueBinder = null;

    public function __construct()
    {
        $this->readFilter = new DefaultReadFilter();
    }

    public function getReadDataOnly(): bool
    {
        return $this->readDataOnly;
    }

    public function setReadDataOnly(bool $readCellValuesOnly): self
    {
        $this->readDataOnly = $readCellValuesOnly;

        return $this;
    }

    public function getReadEmptyCells(): bool
    {
        return $this->readEmptyCells;
    }

    public function setReadEmptyCells(bool $readEmptyCells): self
    {
        $this->readEmptyCells = $readEmptyCells;

        return $this;
    }

    public function getIgnoreRowsWithNoCells(): bool
    {
        return $this->ignoreRowsWithNoCells;
    }

    public function setIgnoreRowsWithNoCells(bool $ignoreRowsWithNoCells): self
    {
        $this->ignoreRowsWithNoCells = $ignoreRowsWithNoCells;

        return $this;
    }

    public function getIncludeCharts(): bool
    {
        return $this->includeCharts;
    }

    public function setIncludeCharts(bool $includeCharts): self
    {
        $this->includeCharts = $includeCharts;

        return $this;
    }

    /** @return null|string[] */
    public function getLoadSheetsOnly(): ?array
    {
        return $this->loadSheetsOnly;
    }

    /** @param null|string|string[] $sheetList */
    public function setLoadSheetsOnly(string|array|null $sheetList): self
    {
        if ($sheetList === null) {
            return $this->setLoadAllSheets();
        }

        $this->loadSheetsOnly = is_array($sheetList) ? $sheetList : [$sheetList];

        return $this;
    }

    public function setLoadAllSheets(): self
    {
        $this->loadSheetsOnly = null;

        return $this;
    }

    public function getReadFilter(): IReadFilter
    {
        return $this->readFilter;
    }

    public function setReadFilter(IReadFilter $readFilter): self
    {
        $this->readFilter = $readFilter;

        return $this;
    }

    /**
     * Allow external images. Use with caution.
     * Improper specification of these within a spreadsheet
     * can subject the caller to security exploits.
     */
    public function setAllowExternalImages(bool $allowExternalImages): self
    {
        $this->allowExternalImages = $allowExternalImages;

        return $this;
    }

    public function getAllowExternalImages(): bool
    {
        return $this->allowExternalImages;
    }

    public function getSecurityScanner(): ?XmlScanner
    {
        return $this->securityScanner;
    }

    public function getSecurityScannerOrThrow(): XmlScanner
    {
        if ($this->securityScanner === null) {
            throw new ReaderException('Security scanner is unexpectedly null');
        }

        return $this->securityScanner;
    }

    protected function processFlags(int $flags): void
    {
        if (((bool) ($flags & self::LOAD_WITH_CHARTS)) === true) {
            $this->setIncludeCharts(true);
        }
        if (((bool) ($flags & self::READ_DATA_ONLY)) === true) {
            $this->setReadDataOnly(true);
        }
        if (((bool) ($flags & self::IGNORE_EMPTY_CELLS)) === true) {
            $this->setReadEmptyCells(false);
        }
        if (((bool) ($flags & self::IGNORE_ROWS_WITH_NO_CELLS)) === true) {
            $this->setIgnoreRowsWithNoCells(true);
        }
        if (((bool) ($flags & self::ALLOW_EXTERNAL_IMAGES)) === true) {
            $this->setAllowExternalImages(true);
        }
        if (((bool) ($flags & self::DONT_ALLOW_EXTERNAL_IMAGES)) === true) {
            $this->setAllowExternalImages(false);
        }
    }

    protected function loadSpreadsheetFromFile(string $filename): Spreadsheet
    {
        throw new PhpSpreadsheetException('Reader classes must implement their own loadSpreadsheetFromFile() method');
    }

    /**
     * Loads Spreadsheet from file.
     *
     * @param int $flags the optional second parameter flags may be used to identify specific elements
     *                       that should be loaded, but which won't be loaded by default, using these values:
     *                            IReader::LOAD_WITH_CHARTS - Include any charts that are defined in the loaded file
     */
    public function load(string $filename, int $flags = 0): Spreadsheet
    {
        $this->processFlags($flags);

        try {
            return $this->loadSpreadsheetFromFile($filename);
        } catch (ReaderException $e) {
            throw $e;
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
            throw new ReaderException('Could not open file ' . $filename . ' for reading.');
        }

        $this->fileHandle = $fileHandle;
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns).
     *
     * @return array<int, array{worksheetName: string, lastColumnLetter: string, lastColumnIndex: int, totalRows: int, totalColumns: int, sheetState: string}>
     */
    public function listWorksheetInfo(string $filename): array
    {
        throw new PhpSpreadsheetException('Reader classes must implement their own listWorksheetInfo() method');
    }

    /**
     * Returns names of the worksheets from a file,
     * possibly without parsing the whole file to a Spreadsheet object.
     * Readers will often have a more efficient method with which
     * they can override this method.
     *
     * @return string[]
     */
    public function listWorksheetNames(string $filename): array
    {
        $returnArray = [];
        $info = $this->listWorksheetInfo($filename);
        foreach ($info as $infoArray) {
            $returnArray[] = $infoArray['worksheetName'];
        }

        return $returnArray;
    }

    public function getValueBinder(): ?IValueBinder
    {
        return $this->valueBinder;
    }

    public function setValueBinder(?IValueBinder $valueBinder): self
    {
        $this->valueBinder = $valueBinder;

        return $this;
    }

    protected function newSpreadsheet(): Spreadsheet
    {
        return new Spreadsheet();
    }
}
