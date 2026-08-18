<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
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

    public function testScalarRowsRoundTrip(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow(['Name', 'Count', 'Ratio', 'Flag']);
        $sheet->appendRow(['Ärger & <Freude>', 42, 1.25, true]);
        $sheet->appendRow([null, null, '  padded  ', false]);
        $writer->close();

        $worksheet = (new XlsxReader())->load($file)->getSheetByNameOrThrow('Data');
        self::assertSame('Name', $worksheet->getCell('A1')->getValue());
        self::assertSame('Ärger & <Freude>', $worksheet->getCell('A2')->getValue());
        self::assertSame(42, $worksheet->getCell('B2')->getValue());
        self::assertSame(1.25, $worksheet->getCell('C2')->getValue());
        self::assertTrue($worksheet->getCell('D2')->getValue());
        self::assertFalse($worksheet->getCell('D3')->getValue());
        self::assertNull($worksheet->getCell('A3')->getValue());
        self::assertSame('  padded  ', $worksheet->getCell('C3')->getValue());
    }

    public function testUnsupportedValueThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $sheet->appendRow([new \stdClass()]);
    }
}
