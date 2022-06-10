<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

abstract class BaseWriter implements IWriter
{
    /**
     * Write charts that are defined in the workbook?
     * Identifies whether the Writer should write definitions for any charts that exist in the PhpSpreadsheet object.
     *
     * @var bool
     */
    protected $includeCharts = false;

    /**
     * Pre-calculate formulas
     * Forces PhpSpreadsheet to recalculate all formulae in a workbook when saving, so that the pre-calculated values are
     * immediately available to MS Excel or other office spreadsheet viewer when opening the file.
     *
     * @var bool
     */
    protected $preCalculateFormulas = true;

    /**
     * Use disk caching where possible?
     *
     * @var bool
     */
    private $useDiskCaching = false;

    /**
     * Disk caching directory.
     *
     * @var string
     */
    private $diskCachingDirectory = './';

    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * @var bool
     */
    private $shouldCloseFile;

    public function getIncludeCharts()
    {
        return $this->includeCharts;
    }

    public function setIncludeCharts($includeCharts)
    {
        $this->includeCharts = (bool) $includeCharts;

        return $this;
    }

    public function getPreCalculateFormulas()
    {
        return $this->preCalculateFormulas;
    }

    public function setPreCalculateFormulas($precalculateFormulas)
    {
        $this->preCalculateFormulas = (bool) $precalculateFormulas;

        return $this;
    }

    public function getUseDiskCaching()
    {
        return $this->useDiskCaching;
    }

    public function setUseDiskCaching($useDiskCache, $cacheDirectory = null)
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

    public function getDiskCachingDirectory()
    {
        return $this->diskCachingDirectory;
    }

    protected function processFlags(int $flags): void
    {
        if (((bool) ($flags & self::SAVE_WITH_CHARTS)) === true) {
            $this->setIncludeCharts(true);
        }
    }

    /**
     * Open file handle.
     *
     * @param resource|string $filename
     */
    public function openFileHandle($filename): void
    {
        if (is_resource($filename)) {
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

    /**
     * Close file handle only if we opened it ourselves.
     */
    protected function maybeCloseFileHandle(): void
    {
        if ($this->shouldCloseFile) {
            if (!fclose($this->fileHandle)) {
                throw new Exception('Could not close file after writing.');
            }
        }
    }
}
