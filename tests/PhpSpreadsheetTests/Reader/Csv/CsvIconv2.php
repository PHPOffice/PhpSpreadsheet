<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Csv;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class CsvIconv2 extends Csv
{
    protected function convertNonUtf8(string $filename): void
    {
        fclose($this->fileHandle);
        $filtered = "php://filter/convert.iconv.{$this->inputEncoding}.utf-8/resource=$filename";
        $sourceStream = fopen($filtered, 'rb');
        if ($sourceStream === false) {
            throw new ReaderException("Unable to get contents of $filename"); // @codeCoverageIgnore
        }
        $fileHandle = fopen('php://memory', 'r+b');
        if ($fileHandle === false) {
            throw new ReaderException('Unable to open php://memory'); // @codeCoverageIgnore
        }
        $bytes = stream_copy_to_stream($sourceStream, $fileHandle);
        fclose($sourceStream);
        if ($bytes === false) {
            throw new ReaderException('Unable to copy stream'); // @codeCoverageIgnore
        }
        $this->fileHandle = $fileHandle;
        $this->skipBOM();
    }
}
