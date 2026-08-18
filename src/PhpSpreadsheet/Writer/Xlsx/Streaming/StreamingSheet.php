<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\Namespaces;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use XMLWriter;

class StreamingSheet
{
    private const TEMP_STREAM = 'php://temp/maxmemory:2097152';

    /** @var resource */
    private $stream;

    private XMLWriter $xmlWriter;

    private bool $headerWritten = false;

    private bool $finished = false;

    private int $rowNumber = 0;

    private int $maxColumn = 0;

    public function __construct(private StreamingWriter $writer)
    {
        $stream = fopen(self::TEMP_STREAM, 'wb+');
        if ($stream === false) {
            // @codeCoverageIgnoreStart
            throw new WriterException('Could not open temporary stream for sheet data.');
            // @codeCoverageIgnoreEnd
        }
        $this->stream = $stream;
        $this->xmlWriter = new XMLWriter();
        $this->xmlWriter->openMemory();
    }

    /**
     * Close sheetData and worksheet, and hand the temp stream to the writer.
     *
     * @return resource
     *
     * @internal called by StreamingWriter only
     */
    public function finish()
    {
        $this->assertUsable();
        if (!$this->headerWritten) {
            $this->writeHeader();
        }
        $this->finished = true;
        fwrite($this->stream, '</sheetData>');
        fwrite($this->stream, '</worksheet>');

        return $this->stream;
    }

    private function writeHeader(): void
    {
        $this->headerWritten = true;
        fwrite($this->stream, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n");
        fwrite($this->stream, '<worksheet xmlns="' . Namespaces::MAIN . '" xmlns:r="' . Namespaces::SCHEMA_OFFICE_DOCUMENT . '">');
        fwrite($this->stream, '<sheetViews><sheetView workbookViewId="0"/></sheetViews>');
        fwrite($this->stream, '<sheetData>');
    }

    private function assertUsable(): void
    {
        if ($this->finished) {
            throw new WriterException('This sheet has been finished; use the sheet returned by the most recent startSheet().');
        }
    }
}
