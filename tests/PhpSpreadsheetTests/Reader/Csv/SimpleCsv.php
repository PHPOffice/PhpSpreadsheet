<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Implements IReader but not IReader2 - no listWorksheetInfo/Names.
// Also minimal error handling and minimal options.
class SimpleCsv implements IReader
{
    protected IReadFilter $readFilter;

    public function __construct()
    {
        $this->readFilter = new DefaultReadFilter();
    }

    public function canRead(string $filename): bool
    {
        return true;
    }

    public function getReadDataOnly(): bool
    {
        return true;
    }

    public function setReadDataOnly(bool $readDataOnly): self
    {
        return $this;
    }

    public function getReadEmptyCells(): bool
    {
        return true;
    }

    public function setReadEmptyCells(bool $readEmptyCells): self
    {
        return $this;
    }

    public function getIncludeCharts(): bool
    {
        return true;
    }

    public function setIncludeCharts(bool $includeCharts): self
    {
        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getLoadSheetsOnly(): ?array
    {
        return [];
    }

    public function setLoadSheetsOnly(string|array|null $value): self
    {
        return $this;
    }

    public function setLoadAllSheets(): self
    {
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

    public function setAllowExternalImages(bool $allowExternalImages): self
    {
        return $this;
    }

    public function getAllowExternalImages(): bool
    {
        return false;
    }

    public function setCreateBlankSheetIfNoneRead(bool $createBlankSheetIfNoneRead): self
    {
        return $this;
    }

    public function load(string $filename, int $flags = 0): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $fh = fopen($filename, 'rb');
        $data = false;
        if ($fh !== false) {
            $data = fgetcsv($fh, escape: '');
            $row = 0;
            while ($data !== false) {
                ++$row;
                $sheet->fromArray($data, null, "A$row", true);
                $data = fgetcsv($fh, escape: '');
            }
        }

        return $spreadsheet;
    }
}
