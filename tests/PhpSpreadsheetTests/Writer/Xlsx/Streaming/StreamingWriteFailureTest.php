<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use DOMDocument;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingSheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;

class StreamingWriteFailureTest extends TestCase
{
    private string $file = '';

    protected function setUp(): void
    {
        $this->file = File::temporaryFilename();
        ControlledStream::$data = '';
        ControlledStream::$writeSize = 7;
        ControlledStream::$bytesUntilFailure = null;
        ControlledStream::$returnFalse = false;
        ControlledStream::$throwOnFailure = false;
        stream_wrapper_register('streamingtest', ControlledStream::class);
    }

    protected function tearDown(): void
    {
        stream_wrapper_unregister('streamingtest');
        if (file_exists($this->file)) {
            unlink($this->file);
        }
        ControlledStream::$data = '';
        ControlledStream::$bytesUntilFailure = null;
        ControlledStream::$returnFalse = false;
        ControlledStream::$throwOnFailure = false;
    }

    private function replaceStream(StreamingSheet $sheet): void
    {
        $property = new ReflectionProperty(StreamingSheet::class, 'stream');
        $original = $property->getValue($sheet);
        if (is_resource($original)) {
            fclose($original);
        }
        $property->setValue($sheet, fopen('streamingtest://sheet', 'wb'));
    }

    public function testPartialWritesPreserveCompleteSheet(): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Data');
        $this->replaceStream($sheet);
        $sheet->setColumnWidths([1 => 12.5]);
        $sheet->freezePane('A2');
        $sheet->setAutoFilterToWrittenRange();
        $sheet->appendRow(['first']);
        $sheet->appendRow(['second']);
        $sheet->finish();

        $xml = new DOMDocument();
        self::assertTrue($xml->loadXML(ControlledStream::$data));
        self::assertSame(2, $xml->getElementsByTagName('row')->length);
        self::assertSame('second', $xml->getElementsByTagName('t')->item(1)?->textContent);
        self::assertSame(1, $xml->getElementsByTagName('autoFilter')->length);
    }

    #[DataProvider('failureProvider')]
    public function testWriteFailureRejectsIncompleteSheet(string $stage, bool $returnFalse): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Data');
        $this->replaceStream($sheet);
        if ($stage !== 'header') {
            $sheet->appendRow(['first']);
        }
        ControlledStream::$returnFalse = $returnFalse;
        ControlledStream::$bytesUntilFailure = 3;

        try {
            if ($stage === 'footer') {
                $writer->close();
            } else {
                $sheet->appendRow(['lost row']);
            }
            self::fail('Expected a write failure.');
        } catch (WriterException $e) {
            self::assertStringContainsString('Could not write all sheet data', $e->getMessage());
        }
        if ($stage !== 'footer') {
            $this->expectException(WriterException::class);
            $this->expectExceptionMessage('undefined state');
            $writer->close();
        }
    }

    public function testExceptionWhileFinishingEmptySheetInvalidatesIt(): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Empty');
        $this->replaceStream($sheet);
        ControlledStream::$bytesUntilFailure = 3;
        ControlledStream::$throwOnFailure = true;

        try {
            $writer->startSheet('Next');
            self::fail('Expected the empty sheet header write to fail.');
        } catch (RuntimeException $e) {
            self::assertSame('Simulated write exception.', $e->getMessage());
        }
        ControlledStream::$bytesUntilFailure = null;
        ControlledStream::$throwOnFailure = false;
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('undefined state');
        $sheet->appendRow(['must not follow a partial header']);
    }

    public static function failureProvider(): array
    {
        return [
            'zero-byte header write' => ['header', false],
            'failed header write' => ['header', true],
            'zero-byte row write' => ['row', false],
            'failed row write' => ['row', true],
            'zero-byte footer write' => ['footer', false],
            'failed footer write' => ['footer', true],
        ];
    }
}
