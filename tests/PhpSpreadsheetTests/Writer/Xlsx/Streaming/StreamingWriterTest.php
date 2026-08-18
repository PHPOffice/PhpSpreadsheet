<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamedCell;
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
        // Note: inline strings are read as RichText by PhpSpreadsheet's reader; cast to string for comparison
        self::assertSame('Name', (string) $worksheet->getCell('A1')->getValue());
        self::assertSame('Ärger & <Freude>', (string) $worksheet->getCell('A2')->getValue());
        self::assertSame(42, $worksheet->getCell('B2')->getValue());
        self::assertSame(1.25, $worksheet->getCell('C2')->getValue());
        self::assertTrue($worksheet->getCell('D2')->getValue());
        self::assertFalse($worksheet->getCell('D3')->getValue());
        self::assertNull($worksheet->getCell('A3')->getValue());
        self::assertSame('  padded  ', (string) $worksheet->getCell('C3')->getValue());
    }

    public function testUnsupportedValueThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $sheet->appendRow([new \stdClass()]);
    }

    public function testFormulaRoundTrip(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow([2, 3]);
        $sheet->appendRow(['=SUM(A1:B1)']);
        $sheet->appendRow([new StreamedCell('=not a formula', null, DataType::TYPE_STRING)]);
        $writer->close();

        $worksheet = (new XlsxReader())->load($file)->getSheetByNameOrThrow('Data');
        self::assertSame('=SUM(A1:B1)', $worksheet->getCell('A2')->getValue());
        self::assertSame(5, $worksheet->getCell('A2')->getCalculatedValue());
        self::assertSame('=not a formula', (string) $worksheet->getCell('A3')->getValue());
    }

    public function testDateTimeRoundTrip(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow([new \DateTimeImmutable('2026-01-02 03:04:05')]);
        $writer->close();

        $worksheet = (new XlsxReader())->load($file)->getSheetByNameOrThrow('Data');
        $cell = $worksheet->getCell('A1');
        self::assertEqualsWithDelta(46024.12783564815, $cell->getValue(), 1E-8);
        self::assertSame(
            NumberFormat::FORMAT_DATE_DATETIME,
            $worksheet->getStyle('A1')->getNumberFormat()->getFormatCode()
        );
    }
}
