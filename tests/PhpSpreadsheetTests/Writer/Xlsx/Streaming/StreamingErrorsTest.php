<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\TestCase;

class StreamingErrorsTest extends TestCase
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

    public function testUnwritableFileThrows(): void
    {
        $this->expectException(WriterException::class);
        new StreamingWriter('/nonexistent-dir-zzz/out.xlsx');
    }

    public function testCloseWithoutSheetsThrows(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('no sheets');
        $writer->close();
    }

    public function testDoubleCloseThrows(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $writer->startSheet('Data');
        $writer->close();
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('already been closed');
        $writer->close();
    }

    public function testStartSheetAfterCloseThrows(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $writer->startSheet('Data');
        $writer->close();
        $this->expectException(WriterException::class);
        $writer->startSheet('More');
    }

    public function testStaleSheetThrows(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $first = $writer->startSheet('First');
        $writer->startSheet('Second');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('finished');
        $first->appendRow(['x']);
    }

    public function testAppendAfterCloseThrows(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $sheet = $writer->startSheet('Data');
        $writer->close();
        $this->expectException(WriterException::class);
        $sheet->appendRow(['x']);
    }

    public function testInvalidSheetNameThrows(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $writer->startSheet('Bad[Name]');
    }

    public function testRegisterStyleAfterCloseThrows(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $writer->startSheet('Data');
        $writer->close();
        $this->expectException(WriterException::class);
        $writer->registerStyle(['font' => ['bold' => true]]);
    }
}
