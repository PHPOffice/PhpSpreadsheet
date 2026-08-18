<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingSheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;
use ZipArchive;

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
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage("Invalid sheet name 'Bad[Name]'");
        $writer->startSheet('Bad[Name]');
    }

    public function testEmptySheetNameThrows(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('cannot be empty');
        $writer->startSheet('');
    }

    public function testWriterStaysConsistentAfterInvalidSheetName(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $writer->startSheet('Good')->appendRow(['x']);

        try {
            $writer->startSheet('Bad[Name]');
            self::fail('Expected a WriterException.');
        } catch (WriterException) {
            // expected; the failed sheet must leave no trace in the workbook
        }

        $writer->startSheet('Recovered')->appendRow(['y']);
        $writer->close();

        $spreadsheet = (new XlsxReader())->load($file);
        self::assertSame(['Good', 'Recovered'], $spreadsheet->getSheetNames());
        $spreadsheet->disconnectWorksheets();

        $zip = new ZipArchive();
        $zip->open($file);
        self::assertIsString($zip->getFromName('xl/worksheets/sheet1.xml'));
        self::assertIsString($zip->getFromName('xl/worksheets/sheet2.xml'));
        self::assertFalse($zip->getFromName('xl/worksheets/sheet3.xml'));
        $zip->close();
    }

    public function testInvalidFirstSheetNameLeavesWriterUsable(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);

        try {
            $writer->startSheet('Bad[Name]');
            self::fail('Expected a WriterException.');
        } catch (WriterException) {
            // expected; the initial shell sheet is reused on retry
        }

        $writer->startSheet('Good')->appendRow(['x']);
        $writer->close();

        $spreadsheet = (new XlsxReader())->load($file);
        self::assertSame(['Good'], $spreadsheet->getSheetNames());
        $spreadsheet->disconnectWorksheets();
    }

    public function testRegisterStyleAfterCloseThrows(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $writer->startSheet('Data');
        $writer->close();
        $this->expectException(WriterException::class);
        $writer->registerStyle(['font' => ['bold' => true]]);
    }

    public function testAppendRowAfterFailureThrowsBrokenState(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $sheet = $writer->startSheet('Data');

        try {
            $sheet->appendRow([new stdClass()]);
            self::fail('Expected a WriterException from the failed appendRow().');
        } catch (WriterException) {
            // expected; the sheet is now broken
        }

        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('undefined state');
        $sheet->appendRow(['x']);
    }

    public function testCloseAfterFailedAppendRowThrows(): void
    {
        $writer = new StreamingWriter($this->tempFile());
        $sheet = $writer->startSheet('Data');

        try {
            $sheet->appendRow([new stdClass()]);
            self::fail('Expected a WriterException from the failed appendRow().');
        } catch (WriterException) {
            // expected; the sheet is now broken
        }

        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('undefined state');
        $writer->close();
    }

    public function testDestructWithoutCloseRemovesFile(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        self::assertFileExists($file);
        unset($writer);
        self::assertFileDoesNotExist($file);
    }

    public function testCloseWithoutSheetsRemovesFile(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        self::assertFileExists($file);

        try {
            $writer->close();
            self::fail('Expected a WriterException.');
        } catch (WriterException) {
            // expected
        }
        self::assertFileDoesNotExist($file);
    }

    public function testCloseWithInvalidFileHandleThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $writer->startSheet('Data')->appendRow(['x']);
        $handleProperty = new ReflectionProperty(StreamingWriter::class, 'fileHandle');
        $handle = $handleProperty->getValue($writer);
        self::assertIsResource($handle);
        fclose($handle);

        try {
            $writer->close();
            self::fail('Expected a WriterException.');
        } catch (WriterException $e) {
            self::assertStringContainsString('no longer valid', $e->getMessage());
        }
        self::assertFileDoesNotExist($file);
    }

    public function testCloseWrapsUnderlyingWriteFailure(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow(['x']);
        $streamProperty = new ReflectionProperty(StreamingSheet::class, 'stream');
        $stream = $streamProperty->getValue($sheet);
        self::assertIsResource($stream);
        fclose($stream);

        try {
            $writer->close();
            self::fail('Expected a WriterException.');
        } catch (WriterException $e) {
            self::assertStringContainsString('Failed to write the Xlsx file', $e->getMessage());
            self::assertNotNull($e->getPrevious());
        }
        self::assertFileDoesNotExist($file);
    }
}
