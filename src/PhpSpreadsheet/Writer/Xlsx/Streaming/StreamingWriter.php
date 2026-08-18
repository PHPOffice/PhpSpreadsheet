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

class StreamingWriter
{
    /** @var resource */
    private $fileHandle;

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
            $fileHandle = fopen($filename, 'wb+');
        } catch (Exception) {
            throw new WriterException("Could not open file $filename for writing.");
        }
        if ($fileHandle === false) {
            throw new WriterException("Could not open file $filename for writing.");
        }
        $this->fileHandle = $fileHandle;
        $this->shell = new Spreadsheet();
        $this->partWriter = new XlsxWriter($this->shell);
    }

    public function startSheet(string $name): StreamingSheet
    {
        $this->assertNotClosed();
        $this->finishActiveSheet();
        $shellSheet = ($this->sheetCount === 0)
            ? $this->shell->getSheet(0)
            : $this->shell->createSheet();
        $shellSheet->setTitle($name);
        ++$this->sheetCount;
        $this->activeSheet = new StreamingSheet($this);

        return $this->activeSheet;
    }

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
            throw new WriterException('Cannot close a streaming writer with no sheets; call startSheet() first.');
        }
        $this->finishActiveSheet();
        $this->closed = true;

        try {
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
            $zip->addFile('xl/workbook.xml', $partWriter->getWriterPartWorkbook()->writeWorkbook($this->shell, false, $this->hasFormulas ? true : null));
            foreach ($this->finishedSheets as $index => $finishedSheet) {
                rewind($finishedSheet['stream']);
                $zip->addFileFromStream('xl/worksheets/sheet' . ($index + 1) . '.xml', $finishedSheet['stream']);
            }
            $zip->finish();
        } finally {
            foreach ($this->finishedSheets as $finishedSheet) {
                fclose($finishedSheet['stream']);
            }
            $this->finishedSheets = [];
            fclose($this->fileHandle);
        }
    }

    public function isStyleIdRegistered(int $styleId): bool
    {
        return $styleId >= 0 && $styleId < count($this->shell->getCellXfCollection());
    }

    public function getDefaultDateStyleId(): int
    {
        if ($this->defaultDateStyleId === null) {
            $this->defaultDateStyleId = $this->registerStyle([
                'numberFormat' => ['formatCode' => NumberFormat::FORMAT_DATE_DATETIME],
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
}
