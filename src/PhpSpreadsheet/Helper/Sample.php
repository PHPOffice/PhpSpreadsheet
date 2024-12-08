<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;
use RuntimeException;
use Throwable;

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
     * @return string[][] [$name => $path]
     */
    public function getSamples()
    {
        // Populate samples
        $baseDir = realpath(__DIR__ . '/../../../samples');
        if ($baseDir === false) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('realpath returned false');
            // @codeCoverageIgnoreEnd
        }
        $directory = new RecursiveDirectoryIterator($baseDir);
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/^.+\.php$/', RecursiveRegexIterator::GET_MATCH);

        $files = [];
        foreach ($regex as $file) {
            $file = str_replace(str_replace('\\', '/', $baseDir) . '/', '', str_replace('\\', '/', $file[0]));
            if (is_array($file)) {
                // @codeCoverageIgnoreStart
                throw new RuntimeException('str_replace returned array');
                // @codeCoverageIgnoreEnd
            }
            $info = pathinfo($file);
            $category = str_replace('_', ' ', $info['dirname'] ?? '');
            $name = str_replace('_', ' ', (string) preg_replace('/(|\.php)/', '', $info['filename']));
            if (!in_array($category, ['.', 'boostrap', 'templates'])) {
                if (!isset($files[$category])) {
                    $files[$category] = [];
                }
                $files[$category][$name] = $file;
            }
        }

        // Sort everything
        ksort($files);
        foreach ($files as &$f) {
            asort($f);
        }

        return $files;
    }

    /**
     * Write documents.
     *
     * @param string $filename
     * @param string[] $writers
     */
    public function write(Spreadsheet $spreadsheet, $filename, array $writers = ['Xlsx', 'Xls'], bool $withCharts = false, ?callable $writerCallback = null): void
    {
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Write documents
        foreach ($writers as $writerType) {
            $path = $this->getFilename($filename, mb_strtolower($writerType));
            $writer = IOFactory::createWriter($spreadsheet, $writerType);
            $writer->setIncludeCharts($withCharts);
            if ($writerCallback !== null) {
                $writerCallback($writer);
            }
            $callStartTime = microtime(true);
            $writer->save($path);
            $this->logWrite($writer, $path, /** @scrutinizer ignore-type */ $callStartTime);
            if ($this->isCli() === false) {
                echo '<a href="/download.php?type=' . pathinfo($path, PATHINFO_EXTENSION) . '&name=' . basename($path) . '">Download ' . basename($path) . '</a><br />';
            }
        }

        $this->logEndingNotes();
    }

    protected function isDirOrMkdir(string $folder): bool
    {
        return \is_dir($folder) || \mkdir($folder);
    }

    /**
     * Returns the temporary directory and make sure it exists.
     *
     * @return string
     */
    public function getTemporaryFolder()
    {
        $tempFolder = sys_get_temp_dir() . '/phpspreadsheet';
        if (!$this->isDirOrMkdir($tempFolder)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $tempFolder));
        }

        return $tempFolder;
    }

    /**
     * Returns the filename that should be used for sample output.
     *
     * @param string $filename
     * @param string $extension
     */
    public function getFilename($filename, $extension = 'xlsx'): string
    {
        $originalExtension = pathinfo($filename, PATHINFO_EXTENSION);

        return $this->getTemporaryFolder() . '/' . str_replace('.' . /** @scrutinizer ignore-type */ $originalExtension, '.' . $extension, basename($filename));
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
        if ($temporaryFilename === false) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException('tempnam returned false');
            // @codeCoverageIgnoreEnd
        }
        unlink($temporaryFilename);

        return $temporaryFilename . '.' . $extension;
    }

    public function log(string $message): void
    {
        $eol = $this->isCli() ? PHP_EOL : '<br />';
        echo($this->isCli() ? date('H:i:s ') : '') . $message . $eol;
    }

    public function renderChart(Chart $chart, string $fileName): void
    {
        if ($this->isCli() === true) {
            return;
        }

        Settings::setChartRenderer(\PhpOffice\PhpSpreadsheet\Chart\Renderer\MtJpGraphRenderer::class);

        $fileName = $this->getFilename($fileName, 'png');

        try {
            $chart->render($fileName);
            $this->log('Rendered image: ' . $fileName);
            $imageData = file_get_contents($fileName);
            if ($imageData !== false) {
                echo '<div><img src="data:image/gif;base64,' . base64_encode($imageData) . '" /></div>';
            }
        } catch (Throwable $e) {
            $this->log('Error rendering chart: ' . $e->getMessage() . PHP_EOL);
        }
    }

    public function titles(string $category, string $functionName, ?string $description = null): void
    {
        $this->log(sprintf('%s Functions:', $category));
        $description === null
            ? $this->log(sprintf('Function: %s()', rtrim($functionName, '()')))
            : $this->log(sprintf('Function: %s() - %s.', rtrim($functionName, '()'), rtrim($description, '.')));
    }

    public function displayGrid(array $matrix): void
    {
        $renderer = new TextGrid($matrix, $this->isCli());
        echo $renderer->render();
    }

    public function logCalculationResult(
        Worksheet $worksheet,
        string $functionName,
        string $formulaCell,
        ?string $descriptionCell = null
    ): void {
        if ($descriptionCell !== null) {
            $this->log($worksheet->getCell($descriptionCell)->getValue());
        }
        $this->log($worksheet->getCell($formulaCell)->getValue());
        $this->log(sprintf('%s() Result is ', $functionName) . $worksheet->getCell($formulaCell)->getCalculatedValue());
    }

    /**
     * Log ending notes.
     */
    public function logEndingNotes(): void
    {
        // Do not show execution time for index
        $this->log('Peak memory usage: ' . (memory_get_peak_usage(true) / 1024 / 1024) . 'MB');
    }

    /**
     * Log a line about the write operation.
     *
     * @param string $path
     * @param float $callStartTime
     */
    public function logWrite(IWriter $writer, $path, $callStartTime): void
    {
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        $reflection = new ReflectionClass($writer);
        $format = $reflection->getShortName();

        $message = ($this->isCli() === true)
            ? "Write {$format} format to {$path}  in " . sprintf('%.4f', $callTime) . ' seconds'
            : "Write {$format} format to <code>{$path}</code>  in " . sprintf('%.4f', $callTime) . ' seconds';

        $this->log($message);
    }

    /**
     * Log a line about the read operation.
     *
     * @param string $format
     * @param string $path
     * @param float $callStartTime
     */
    public function logRead($format, $path, $callStartTime): void
    {
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        $message = "Read {$format} format from <code>{$path}</code>  in " . sprintf('%.4f', $callTime) . ' seconds';

        $this->log($message);
    }
}
