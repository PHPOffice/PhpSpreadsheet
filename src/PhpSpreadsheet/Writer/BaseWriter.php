<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

abstract class BaseWriter implements IWriter
{
    /**
     * Write charts that are defined in the workbook?
     * Identifies whether the Writer should write definitions for any charts that exist in the PhpSpreadsheet object.
     */
    protected bool $includeCharts = false;

    /**
     * Pre-calculate formulas
     * Forces PhpSpreadsheet to recalculate all formulae in a workbook when saving, so that the pre-calculated values are
     * immediately available to MS Excel or other office spreadsheet viewer when opening the file.
     */
    protected bool $preCalculateFormulas = true;

    /**
     * Write Windows Metafiles (WMF, EMF, EMF+) as PNG images?
     * Writers whose format can not hold metafiles (Xls, Html, Pdf) always write them as PNG images.
     */
    protected bool $convertMetafilesToPng = true;

    /**
     * Use disk caching where possible?
     */
    private bool $useDiskCaching = false;

    /**
     * Disk caching directory.
     */
    private string $diskCachingDirectory = './';

    /**
     * @var resource
     */
    protected $fileHandle;

    private bool $shouldCloseFile;

    public function getIncludeCharts(): bool
    {
        return $this->includeCharts;
    }

    public function setIncludeCharts(bool $includeCharts): self
    {
        $this->includeCharts = $includeCharts;

        return $this;
    }

    public function getPreCalculateFormulas(): bool
    {
        return $this->preCalculateFormulas;
    }

    public function setPreCalculateFormulas(bool $precalculateFormulas): self
    {
        $this->preCalculateFormulas = $precalculateFormulas;

        return $this;
    }

    /**
     * Get whether Windows Metafiles (WMF, EMF, EMF+) are written as PNG images.
     */
    public function getConvertMetafilesToPng(): bool
    {
        return $this->convertMetafilesToPng;
    }

    /**
     * Set whether Windows Metafiles (WMF, EMF, EMF+) are written as PNG images, or stored as is.
     *
     * PNG images are displayed the same way by all applications.
     * Metafiles stored as is keep their vector graphics, but some applications
     * (like LibreOffice, Excel for Mac, Excel for the web or Google Sheets) do not support all their records.
     * A metafile which can not be rendered is always stored as is.
     *
     * This setting is used by the Xlsx (default: stored as is) and Ods (default: PNG) writers.
     * Writers whose format can not hold metafiles (Xls, Html, Pdf) always write them as PNG images.
     *
     * @return $this
     */
    public function setConvertMetafilesToPng(bool $convertMetafilesToPng): static
    {
        $this->convertMetafilesToPng = $convertMetafilesToPng;

        return $this;
    }

    public function getUseDiskCaching(): bool
    {
        return $this->useDiskCaching;
    }

    public function setUseDiskCaching(bool $useDiskCache, ?string $cacheDirectory = null): self
    {
        $this->useDiskCaching = $useDiskCache;

        if ($cacheDirectory !== null) {
            if (is_dir($cacheDirectory)) {
                $this->diskCachingDirectory = $cacheDirectory;
            } else {
                throw new Exception("Directory does not exist: $cacheDirectory");
            }
        }

        return $this;
    }

    public function getDiskCachingDirectory(): string
    {
        return $this->diskCachingDirectory;
    }

    protected function processFlags(int $flags): void
    {
        if (((bool) ($flags & self::SAVE_WITH_CHARTS)) === true) {
            $this->setIncludeCharts(true);
        }
        if (((bool) ($flags & self::DISABLE_PRECALCULATE_FORMULAE)) === true) {
            $this->setPreCalculateFormulas(false);
        }
    }

    /**
     * Open file handle.
     *
     * @param resource|string $filename
     */
    public function openFileHandle($filename): void
    {
        if (!is_string($filename)) {
            $this->fileHandle = $filename;
            $this->shouldCloseFile = false;

            return;
        }

        $mode = 'wb';
        $scheme = parse_url($filename, PHP_URL_SCHEME);
        if ($scheme === 's3') {
            // @codeCoverageIgnoreStart
            $mode = 'w';
            // @codeCoverageIgnoreEnd
        }
        $fileHandle = $filename ? fopen($filename, $mode) : false;
        if ($fileHandle === false) {
            throw new Exception('Could not open file "' . $filename . '" for writing.');
        }

        $this->fileHandle = $fileHandle;
        $this->shouldCloseFile = true;
    }

    protected function tryClose(): bool
    {
        return fclose($this->fileHandle);
    }

    /**
     * Close file handle only if we opened it ourselves.
     */
    protected function maybeCloseFileHandle(): void
    {
        if ($this->shouldCloseFile) {
            if (!$this->tryClose()) {
                throw new Exception('Could not close file after writing.');
            }
        }
    }
}
