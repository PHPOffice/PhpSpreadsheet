<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Writer\ZipStream0;
use Throwable;

class StreamingWriter
{
    /** @var resource */
    private $fileHandle;

    private string $filename;

    private ?string $temporaryFilename = null;

    private ?int $filePermissions = null;

    private Spreadsheet $shell;

    private XlsxWriter $partWriter;

    private ?StreamingSheet $activeSheet = null;

    /** @var array<int, array{stream: resource}> */
    private array $finishedSheets = [];

    private int $sheetCount = 0;

    private bool $closed = false;

    private bool $hasFormulas = false;

    private ?int $defaultDateStyleId = null;

    /** @var array<string, int> Style hash => cell XF index. */
    private array $styleIds = [];

    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->shell = new Spreadsheet();
        $this->partWriter = new XlsxWriter($this->shell);
        $this->styleIds[$this->shell->getCellXfByIndex(0)->getHashCode()] = 0;
        $fileHandle = false;

        try {
            $output = $filename;
            if (!str_contains($filename, '://')) {
                $directory = realpath(dirname($filename));
                if ($directory === false || !is_writable($directory)) {
                    throw new WriterException("Could not open file $filename for writing.");
                }
                if (file_exists($filename) && (!is_file($filename) || !is_writable($filename))) {
                    throw new WriterException("Could not open file $filename for writing.");
                }
                $this->filename = $directory . DIRECTORY_SEPARATOR . basename($filename);
                $permissions = file_exists($filename) ? fileperms($filename) : (0o666 & ~umask());
                $this->filePermissions = $permissions === false ? null : ($permissions & 0o777);
                $temporaryFilename = tempnam($directory, '.phpspreadsheet-');
                if ($temporaryFilename === false) {
                    throw new WriterException('Could not create temporary output file.');
                }
                $this->temporaryFilename = $temporaryFilename;
                if (dirname($temporaryFilename) !== $directory) {
                    throw new WriterException('Temporary output must be in the destination directory.');
                }
                $output = $temporaryFilename;
            }
            $fileHandle = fopen($output, 'wb');
            if ($fileHandle === false) {
                throw new WriterException("Could not open file $filename for writing.");
            }
            $this->fileHandle = $fileHandle;
        } catch (Throwable $e) {
            if (is_resource($fileHandle)) {
                fclose($fileHandle);
            }
            $this->removeTemporaryFile();

            throw new WriterException("Could not open file $filename for writing: " . $e->getMessage(), 0, $e);
        }
    }

    public function __destruct()
    {
        $this->abort();
    }

    /** Discard unfinished output and release its streams. Safe after close(). */
    public function abort(): void
    {
        if (!$this->closed) {
            $this->closed = true;
            $this->closeSheetStreams();
            $this->closeOutput();
        }
    }

    public function startSheet(string $name): StreamingSheet
    {
        $this->assertNotClosed();
        if ($name === '') {
            throw new WriterException('Sheet name cannot be empty.');
        }
        $this->finishActiveSheet();
        $shellSheet = ($this->sheetCount === 0)
            ? $this->shell->getSheet(0)
            : $this->shell->createSheet();

        try {
            $shellSheet->setTitle($name);
        } catch (Throwable $e) {
            if ($this->sheetCount > 0) {
                // roll back createSheet() so the shell workbook only lists sheets that have a stream
                $this->shell->removeSheetByIndex($this->shell->getIndex($shellSheet));
            }

            throw new WriterException("Invalid sheet name '$name': " . $e->getMessage(), 0, $e);
        }
        ++$this->sheetCount;
        $this->activeSheet = new StreamingSheet($this);

        return $this->activeSheet;
    }

    /** @param mixed[] $styleArray */
    public function registerStyle(array $styleArray): int
    {
        $this->assertNotClosed();
        $style = new Style();
        $style->applyFromArray($styleArray);
        $hash = $style->getHashCode();
        if (isset($this->styleIds[$hash])) {
            return $this->styleIds[$hash];
        }
        $this->shell->addCellXf($style);
        $this->styleIds[$hash] = $style->getIndex();

        return $style->getIndex();
    }

    public function close(): void
    {
        $this->assertNotClosed();
        if ($this->sheetCount === 0) {
            $this->closed = true;
            $this->closeOutput();

            throw new WriterException('Cannot close a streaming writer with no sheets; call startSheet() first.');
        }
        $this->closed = true;

        try {
            if (!is_resource($this->fileHandle)) {
                // ZipStream silently falls back to php://output for a non-resource
                throw new WriterException('The output file handle is no longer valid.');
            }
            $this->finishActiveSheet();
            $zip = ZipStream0::newZipStream($this->fileHandle);
            $partWriter = $this->partWriter;
            $partWriter->createStyleDictionaries();
            $zip->addFile('[Content_Types].xml', $partWriter->getWriterPartContentTypes()->writeContentTypes($this->shell, false));
            $zip->addFile('_rels/.rels', $partWriter->getWriterPartRels()->writeRelationships($this->shell));
            $zip->addFile('xl/_rels/workbook.xml.rels', $partWriter->getWriterPartRels()->writeWorkbookRelationships($this->shell));
            $zip->addFile('docProps/app.xml', $partWriter->getWriterPartDocProps()->writeDocPropsApp($this->shell));
            $zip->addFile('docProps/core.xml', $partWriter->getWriterPartDocProps()->writeDocPropsCore($this->shell));
            $zip->addFile('xl/theme/theme1.xml', $partWriter->getWriterPartTheme()->writeTheme($this->shell));
            $zip->addFile('xl/sharedStrings.xml', $partWriter->getWriterPartStringTable()->writeStringTable([]));
            $zip->addFile('xl/styles.xml', $partWriter->getWriterPartStyle()->writeStyles($this->shell));
            // Recalculate uncached formulas on load without enabling forceFullCalc,
            // which also changes calculation behavior in other open Excel workbooks.
            $zip->addFile('xl/workbook.xml', $partWriter->getWriterPartWorkbook()->writeWorkbook($this->shell, !$this->hasFormulas, XlsxWriter::DEFAULT_FORCE_FULL_CALC));
            foreach ($this->finishedSheets as $index => $finishedSheet) {
                rewind($finishedSheet['stream']);
                $zip->addFileFromStream('xl/worksheets/sheet' . ($index + 1) . '.xml', $finishedSheet['stream']);
            }
            $zip->finish();
            if (!fflush($this->fileHandle) || !fclose($this->fileHandle)) {
                throw new WriterException('Could not close the output file after writing.');
            }
            if ($this->temporaryFilename !== null) {
                if ($this->filePermissions !== null) {
                    @chmod($this->temporaryFilename, $this->filePermissions);
                }
                if (!rename($this->temporaryFilename, $this->filename)) {
                    throw new WriterException('Could not replace the destination with the completed Xlsx file.');
                }
                $this->temporaryFilename = null;
            }
        } catch (Throwable $e) {
            $this->closeSheetStreams();
            $this->closeOutput();
            if ($e instanceof WriterException) {
                throw $e;
            }

            throw new WriterException('Failed to write the Xlsx file: ' . $e->getMessage(), 0, $e);
        }

        $this->closeSheetStreams();
    }

    public function isStyleIdRegistered(int $styleId): bool
    {
        return $styleId >= 0 && $styleId < count($this->shell->getCellXfCollection());
    }

    /** @internal Calendar used by the shell workbook and streamed date values. */
    public function getExcelCalendar(): int
    {
        return $this->shell->getExcelCalendar();
    }

    public function getDefaultDateStyleId(): int
    {
        if ($this->defaultDateStyleId === null) {
            $this->defaultDateStyleId = $this->registerStyle([
                'numberFormat' => ['formatCode' => NumberFormat::FORMAT_DATE_DATETIME_BETTER],
            ]);
        }

        return $this->defaultDateStyleId;
    }

    public function noteFormulaWritten(): void
    {
        $this->hasFormulas = true;
    }

    private function finishActiveSheet(): void
    {
        if ($this->activeSheet !== null) {
            $this->finishedSheets[] = ['stream' => $this->activeSheet->finish()];
            $this->activeSheet = null;
        }
    }

    private function assertNotClosed(): void
    {
        if ($this->closed) {
            throw new WriterException('This streaming writer has already been closed.');
        }
    }

    private function closeSheetStreams(): void
    {
        $this->activeSheet?->discard();
        $this->activeSheet = null;
        foreach ($this->finishedSheets as $finishedSheet) {
            if (is_resource($finishedSheet['stream'])) {
                fclose($finishedSheet['stream']);
            }
        }
        $this->finishedSheets = [];
    }

    private function closeOutput(): void
    {
        if (is_resource($this->fileHandle)) {
            fclose($this->fileHandle);
        }
        $this->removeTemporaryFile();
    }

    private function removeTemporaryFile(): void
    {
        if ($this->temporaryFilename !== null) {
            // Cleanup must not mask the original failure or throw from a destructor.
            @unlink($this->temporaryFilename);
            $this->temporaryFilename = null;
        }
    }
}
