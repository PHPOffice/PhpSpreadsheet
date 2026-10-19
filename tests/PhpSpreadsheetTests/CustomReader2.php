<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Reader\DefaultReadFilter;
use PhpOffice\PhpSpreadsheet\Reader\EmptyWorksheetInfo;
use PhpOffice\PhpSpreadsheet\Reader\IReader2;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Used in IOFactoryRegister tests
class CustomReader2 extends EmptyWorksheetInfo implements IReader2
{
    private bool $readDataOnly = true;

    private bool $readEmptyCells = true;

    private bool $includeCharts = true;

    private bool $allowExternalImages = true;

    private bool $createBlankSheetIfNoneRead = true;

    /** @var string[] */
    private array $loadSheetsOnly = [];

    private IReadFilter $readFilter;

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
        return $this->readDataOnly;
    }

    public function setReadDataOnly(bool $readDataOnly): self
    {
        $this->readDataOnly = $readDataOnly;

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

    public function getIncludeCharts(): bool
    {
        return $this->includeCharts;
    }

    public function setIncludeCharts(bool $includeCharts): self
    {
        $this->includeCharts = $includeCharts;

        return $this;
    }

    /** @return string[] */
    public function getLoadSheetsOnly(): array
    {
        return $this->loadSheetsOnly;
    }

    /** @param null|string|string[] $loadSheetsOnly */
    public function setLoadSheetsOnly(string|array|null $loadSheetsOnly): self
    {
        if (is_array($loadSheetsOnly)) {
            $this->loadSheetsOnly = $loadSheetsOnly;
        }

        return $this;
    }

    public function setLoadAllSheets(): self
    {
        $this->loadSheetsOnly = [];

        return $this;
    }

    public function setReadFilter(IReadFilter $readFilter): self
    {
        $this->readFilter = $readFilter;

        return $this;
    }

    public function getReadFilter(): IReadFilter
    {
        return $this->readFilter;
    }

    public function getAllowExternalImages(): bool
    {
        return $this->allowExternalImages;
    }

    public function setAllowExternalImages(bool $allowExternalImages): self
    {
        $this->allowExternalImages = $allowExternalImages;

        return $this;
    }

    public function getCreateBlankSheetIfNoneRead(): bool
    {
        return $this->createBlankSheetIfNoneRead;
    }

    public function setCreateBlankSheetIfNoneRead(bool $createBlankSheetIfNoneRead): self
    {
        $this->createBlankSheetIfNoneRead = $createBlankSheetIfNoneRead;

        return $this;
    }

    public function load(string $filename, int $flags = 0): Spreadsheet
    {
        return new Spreadsheet();
    }
}
