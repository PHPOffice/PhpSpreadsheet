<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming;

use Exception;
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

    private Spreadsheet $shell;

    private XlsxWriter $partWriter;

    private ?StreamingSheet $activeSheet = null;

    /** @var array<int, array{stream: resource}> */
    private array $finishedSheets = [];

    private int $sheetCount = 0;

    private bool $closed = false;

    private bool $hasFormulas = false;

    private ?int $defaultDateStyleId = null;

    public function __construct(string $filename)
    {
        try {
            $fileHandle = fopen($filename, 'wb');
        } catch (Exception) {
            $fileHandle = false;
        }
        if ($fileHandle === false) {
            throw new WriterException("Could not open file $filename for writing.");
        }
        $this->filename = $filename;
        $this->fileHandle = $fileHandle;
        $this->shell = new Spreadsheet();
        $this->partWriter = new XlsxWriter($this->shell);
    }

    public function __destruct()
    {
        if (!$this->closed) {
            $this->closeSheetStreams();
            $this->closeFileHandleAndUnlink();
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
        $this->shell->addCellXf($style);

        return $style->getIndex();
    }

    public function close(): void
    {
        $this->assertNotClosed();
        if ($this->sheetCount === 0) {
            $this->closed = true;
            $this->closeFileHandleAndUnlink();

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
            $zip->addFile('xl/workbook.xml', $partWriter->getWriterPartWorkbook()->writeWorkbook($this->shell, !$this->hasFormulas, $this->hasFormulas));
            foreach ($this->finishedSheets as $index => $finishedSheet) {
                rewind($finishedSheet['stream']);
                $zip->addFileFromStream('xl/worksheets/sheet' . ($index + 1) . '.xml', $finishedSheet['stream']);
            }
            $zip->finish();
        } catch (Throwable $e) {
            $this->closeSheetStreams();
            $this->closeFileHandleAndUnlink();
            if ($e instanceof WriterException) {
                throw $e;
            }

            throw new WriterException('Failed to write the Xlsx file: ' . $e->getMessage(), 0, $e);
        }

        $this->closeSheetStreams();
        fclose($this->fileHandle);
    }

    public function isStyleIdRegistered(int $styleId): bool
    {
        return $styleId >= 0 && $styleId < count($this->shell->getCellXfCollection());
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
        foreach ($this->finishedSheets as $finishedSheet) {
            if (is_resource($finishedSheet['stream'])) {
                fclose($finishedSheet['stream']);
            }
        }
        $this->finishedSheets = [];
    }

    private function closeFileHandleAndUnlink(): void
    {
        if (is_resource($this->fileHandle)) {
            fclose($this->fileHandle);
        }
        if (file_exists($this->filename)) {
            unlink($this->filename);
        }
    }
}
