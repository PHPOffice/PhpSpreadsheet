<?php

namespace PhpOffice\PhpSpreadsheet\Helper;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Renderer\MtJpGraphRenderer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
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
     */
    public function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Return the filename currently being executed.
     */
    public function getScriptFilename(): string
    {
        return basename(StringHelper::convertToString($_SERVER['SCRIPT_FILENAME']), '.php');
    }

    /**
     * Whether we are executing the index page.
     */
    public function isIndex(): bool
    {
        return $this->getScriptFilename() === 'index';
    }

    /**
     * Return the page title.
     */
    public function getPageTitle(): string
    {
        return $this->isIndex() ? 'PHPSpreadsheet' : $this->getScriptFilename();
    }

    /**
     * Return the page heading.
     */
    public function getPageHeading(): string
    {
        return $this->isIndex() ? '' : '<h1>' . str_replace('_', ' ', $this->getScriptFilename()) . '</h1>';
    }

    /**
     * Returns an array of all known samples.
     *
     * @return string[][] [$name => $path]
     */
    public function getSamples(): array
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
        /** @var string[] $file */
        foreach ($regex as $file) {
            $file = str_replace(str_replace('\\', '/', $baseDir) . '/', '', str_replace('\\', '/', $file[0]));
            $info = pathinfo($file);
            $category = str_replace('_', ' ', $info['dirname'] ?? '');
            $name = str_replace('_', ' ', (string) preg_replace('/(|\.php)/', '', $info['filename']));
            if (!in_array($category, ['.', 'bootstrap', 'templates']) && $name !== 'Header') {
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
     * @param string[] $writers
     */
    public function write(Spreadsheet $spreadsheet, string $filename, array $writers = ['Xlsx', 'Xls'], bool $withCharts = false, ?callable $writerCallback = null, bool $resetActiveSheet = true): void
    {
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        if ($resetActiveSheet) {
            $spreadsheet->setActiveSheetIndex(0);
        }

        // Write documents
        foreach ($writers as $writerType) {
            $path = $this->getFilename($filename, mb_strtolower($writerType));
            if (preg_match('/(mpdf|tcpdf)$/', $path)) {
                $path .= '.pdf';
            }
            $writer = IOFactory::createWriter($spreadsheet, $writerType);
            $writer->setIncludeCharts($withCharts);
            if ($writerCallback !== null) {
                $writerCallback($writer);
            }
            $callStartTime = microtime(true);
            $writer->save($path);
            $this->logWrite($writer, $path, $callStartTime);
            if ($this->isCli() === false) {
                // @codeCoverageIgnoreStart
                echo '<a href="/download.php?type=' . pathinfo($path, PATHINFO_EXTENSION) . '&name=' . basename($path) . '">Download ' . basename($path) . '</a><br />';
                // @codeCoverageIgnoreEnd
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
     */
    public function getTemporaryFolder(): string
    {
        $tempFolder = sys_get_temp_dir() . '/phpspreadsheet';
        if (!$this->isDirOrMkdir($tempFolder)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $tempFolder));
        }

        return $tempFolder;
    }

    /**
     * Returns the filename that should be used for sample output.
     */
    public function getFilename(string $filename, string $extension = 'xlsx'): string
    {
        $originalExtension = pathinfo($filename, PATHINFO_EXTENSION);

        return $this->getTemporaryFolder() . '/' . str_replace('.' . $originalExtension, '.' . $extension, basename($filename));
    }

    /**
     * Return a random temporary file name.
     */
    public function getTemporaryFilename(string $extension = 'xlsx'): string
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

    public function log(mixed $message): void
    {
        $eol = $this->isCli() ? PHP_EOL : '<br />';
        echo ($this->isCli() ? date('H:i:s ') : '') . StringHelper::convertToString($message) . $eol;
    }

    /**
     * Render chart as part of running chart samples in browser.
     * Charts are not rendered in unit tests, which are command line.
     *
     * @codeCoverageIgnore
     */
    public function renderChart(Chart $chart, string $fileName, ?Spreadsheet $spreadsheet = null): void
    {
        if ($this->isCli() === true) {
            return;
        }
        Settings::setChartRenderer(MtJpGraphRenderer::class);

        $fileName = $this->getFilename($fileName, 'png');
        $title = $chart->getTitle();
        $caption = null;
        if ($title !== null) {
            $calculatedTitle = $title->getCalculatedTitle($spreadsheet);
            if ($calculatedTitle !== null) {
                $caption = $title->getCaption();
                $title->setCaption($calculatedTitle);
            }
        }

        try {
            $chart->render($fileName);
            $this->log('Rendered image: ' . $fileName);
            $imageData = @file_get_contents($fileName);
            if ($imageData !== false) {
                echo '<div><img src="data:image/gif;base64,' . base64_encode($imageData) . '" /></div>';
            } else {
                $this->log('Unable to open chart' . PHP_EOL);
            }
        } catch (Throwable $e) {
            $this->log('Error rendering chart: ' . $e->getMessage() . PHP_EOL);
        }
        if (isset($title, $caption)) {
            $title->setCaption($caption);
        }
        Settings::unsetChartRenderer();
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
            $this->log($worksheet->getCell($descriptionCell)->getValueString());
        }
        $this->log($worksheet->getCell($formulaCell)->getValueString());
        $this->log(sprintf('%s() Result is ', $functionName) . $worksheet->getCell($formulaCell)->getCalculatedValueString());
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
     */
    public function logWrite(IWriter $writer, string $path, float $callStartTime): void
    {
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        $reflection = new ReflectionClass($writer);
        $format = $reflection->getShortName();

        $codePath = $this->isCli() ? $path : "<code>$path</code>";
        $message = "Write {$format} format to {$codePath}  in " . sprintf('%.4f', $callTime) . ' seconds';

        $this->log($message);
    }

    /**
     * Log a line about the read operation.
     */
    public function logRead(string $format, string $path, float $callStartTime): void
    {
        $callEndTime = microtime(true);
        $callTime = $callEndTime - $callStartTime;
        $message = "Read {$format} format from <code>{$path}</code>  in " . sprintf('%.4f', $callTime) . ' seconds';

        $this->log($message);
    }
}
