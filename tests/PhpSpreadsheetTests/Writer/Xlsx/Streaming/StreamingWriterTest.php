<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\TestCase;

class StreamingWriterTest extends TestCase
{
    /** @var string[] */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->tempFiles = [];
    }

    private function tempFile(): string
    {
        $file = File::temporaryFilename();
        $this->tempFiles[] = $file;

        return $file;
    }

    public function testEmptySheetsRoundTrip(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $writer->startSheet('First');
        $writer->startSheet('Second Sheet');
        $writer->close();

        $spreadsheet = (new XlsxReader())->load($file);
        self::assertSame(['First', 'Second Sheet'], $spreadsheet->getSheetNames());
        $spreadsheet->disconnectWorksheets();
    }
}
