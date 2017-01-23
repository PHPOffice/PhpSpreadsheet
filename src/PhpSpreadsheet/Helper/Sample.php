<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Helper class to be used in sample code.
 */
class Sample
{
    /**
     * Returns whether we run on CLI or browser.
     *
     * @return bool
     */
    public function isCli()
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Return the filename currently being executed.
     *
     * @return string
     */
    public function getScriptFilename()
    {
        return basename($_SERVER['SCRIPT_FILENAME'], '.php');
    }

    /**
     * Whether we are executing the index page.
     *
     * @return bool
     */
    public function isIndex()
    {
        return $this->getScriptFilename() === 'index';
    }

    /**
     * Return the page title.
     *
     * @return string
     */
    public function getPageTitle()
    {
        return $this->isIndex() ? 'PHPSpreadsheet' : $this->getScriptFilename();
    }

    /**
     * Return the page heading.
     *
     * @return string
     */
    public function getPageHeading()
    {
        return $this->isIndex() ? '' : '<h1>' . str_replace('_', ' ', $this->getScriptFilename()) . '</h1>';
    }

    /**
     * Returns an array of all known samples.
     *
     * @return string[] [$name => $path]
     */
    public function getSamples()
    {
        // Populate samples
        $files = [];
        foreach (glob(realpath(__DIR__ . '/../../../samples') . '/*.php') as $file) {
            $info = pathinfo($file);
            $name = str_replace('_', ' ', preg_replace('/(|\.php)/', '', $info['filename']));
            if (preg_match('/^\d+/', $name)) {
                $files[$name] = $file;
            }
        }

        return $files;
    }

    /**
     * Write documents.
     *
     * @param Spreadsheet $spreadsheet
     * @param string $filename
     * @param string[] $writers
     */
    public function write(Spreadsheet $spreadsheet, $filename, array $writers = ['Xlsx', 'Xls'])
    {
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Write documents
        foreach ($writers as $writerType) {
            $path = $this->getFilename($filename, mb_strtolower($writerType));
            $writer = IOFactory::createWriter($spreadsheet, $writerType);
            $callStartTime = microtime(true);
            $writer->save($path);
            $this->logWrite($writer, $path, $callStartTime);
        }

        $this->logEndingNotes();
    }

    /**
     * Returns the temporary directory and make sure it exists.
     *
     * @return string
     */
    private function getTemporaryFolder()
    {
        $tempFolder = sys_get_temp_dir() . '/phpspreadsheet';
        if (!is_dir($tempFolder)) {
            mkdir($tempFolder);
        }

        return $tempFolder;
    }

    /**
     * Returns the filename that should be used for sample output.
     *
     * @param string $filename
     * @param string $extension
     *
     * @return string
     */
    public function getFilename($filename, $extension = 'xlsx')
    {
        return $this->getTemporaryFolder() . '/' . str_replace('.php', '.' . $extension, basename($filename));
    }

    /**
     * Return a random temporary file name.
     *
     * @param string $extension
     *
     * @return string
     */
    public function getTemporaryFilename($extension = 'xlsx')
    {
        $temporaryFilename = tempnam($this->getTemporaryFolder(), 'phpspreadsheet-');
        unlink($temporaryFilename);

        return $temporaryFilename . '.' . $extension;
    }

    public function log($message)
    {
        echo date('H:i:s '), $message, EOL;
    }

    /**
     * Log ending notes.
     */
    public function logEndingNotes()
    {
        // Do not show execution time for index
        $this->log('Peak memory usage: ' . (memory_get_peak_usage(true) / 1024 / 1024) . 'MB');
    }

    /**
     * Log a line about the write operation.
     *
     * @param \PhpOffice\PhpSpreadsheet\Writer\IWriter $writer
     * @param string $path
     * @param float $callStartTime
     */
    public function logWrite(\PhpOffice\PhpSpreadsheet\Writer\IWriter $writer, $path, $callStartTime)
    {
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        $reflection = new \ReflectionClass($writer);
        $format = $reflection->getShortName();
        $message = "Write {$format} format to <code>{$path}</code>  in " . sprintf('%.4f', $callTime) . ' seconds';

        $this->log($message);
    }

    /**
     * Log a line about the read operation.
     *
     * @param string $format
     * @param string $path
     * @param float $callStartTime
     */
    public function logRead($format, $path, $callStartTime)
    {
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        $message = "Read {$format} format from <code>{$path}</code>  in " . sprintf('%.4f', $callTime) . ' seconds';

        $this->log($message);
    }
}
