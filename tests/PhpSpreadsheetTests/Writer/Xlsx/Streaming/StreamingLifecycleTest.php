<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingSheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;

class StreamingLifecycleTest extends TestCase
{
    private string $file = '';

    protected function setUp(): void
    {
        $this->file = File::temporaryFilename();
        file_put_contents($this->file, 'previous workbook');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function testSuccessfulCloseReplacesExistingFile(): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow([42]);
        self::assertSame('previous workbook', file_get_contents($this->file));
        $temporary = (new ReflectionProperty(StreamingWriter::class, 'temporaryFilename'))->getValue($writer);
        self::assertIsString($temporary);
        self::assertSame(realpath(dirname($this->file)), dirname($temporary));
        $permissions = fileperms($this->file);
        $writer->close();
        $writer->abort();
        self::assertFileDoesNotExist($temporary);
        clearstatcache();
        self::assertSame($permissions, fileperms($this->file));
        $spreadsheet = (new Xlsx())->load($this->file);
        self::assertSame(42, $spreadsheet->getActiveSheet()->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testAbortPreservesDestinationAndClosesEverySheet(): void
    {
        $writer = new StreamingWriter($this->file);
        $first = $writer->startSheet('First');
        $first->appendRow(['first']);
        $firstHandle = (new ReflectionProperty(StreamingSheet::class, 'stream'))->getValue($first);
        $second = $writer->startSheet('Second');
        $second->appendRow(['second']);
        $secondHandle = (new ReflectionProperty(StreamingSheet::class, 'stream'))->getValue($second);
        $temporary = (new ReflectionProperty(StreamingWriter::class, 'temporaryFilename'))->getValue($writer);
        self::assertIsString($temporary);
        $writer->abort();
        $writer->abort();
        self::assertFalse(is_resource($firstHandle));
        self::assertFalse(is_resource($secondHandle));
        self::assertFileDoesNotExist($temporary);
        self::assertSame('previous workbook', file_get_contents($this->file));
        $this->expectException(WriterException::class);
        $second->appendRow(['too late']);
    }

    public function testFailedClosePreservesDestinationAndClosesActiveSheet(): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Data');
        $handle = (new ReflectionProperty(StreamingSheet::class, 'stream'))->getValue($sheet);
        $temporary = (new ReflectionProperty(StreamingWriter::class, 'temporaryFilename'))->getValue($writer);
        self::assertIsString($temporary);

        try {
            $sheet->appendRow([new stdClass()]);
            self::fail('Expected unsupported data to fail.');
        } catch (WriterException) {
            // close must release the broken active sheet too
        }

        try {
            $writer->close();
            self::fail('Expected the broken sheet to prevent close.');
        } catch (WriterException $e) {
            self::assertStringContainsString('undefined state', $e->getMessage());
        }
        self::assertFalse(is_resource($handle));
        self::assertFileDoesNotExist($temporary);
        self::assertSame('previous workbook', file_get_contents($this->file));
    }

    public function testDestructorPreservesExistingFile(): void
    {
        $writer = new StreamingWriter($this->file);
        $temporary = (new ReflectionProperty(StreamingWriter::class, 'temporaryFilename'))->getValue($writer);
        self::assertIsString($temporary);
        unset($writer);
        self::assertSame('previous workbook', file_get_contents($this->file));
        self::assertFileDoesNotExist($temporary);
    }

    public function testDestructorCollectsActiveSheetStorage(): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Data');
        $handle = (new ReflectionProperty(StreamingSheet::class, 'stream'))->getValue($sheet);
        $temporary = (new ReflectionProperty(StreamingWriter::class, 'temporaryFilename'))->getValue($writer);
        self::assertIsString($temporary);
        unset($writer, $sheet);
        gc_collect_cycles();
        self::assertFalse(is_resource($handle));
        self::assertFileDoesNotExist($temporary);
        self::assertSame('previous workbook', file_get_contents($this->file));
    }

    public function testNewDestinationAppearsOnlyAfterClose(): void
    {
        unlink($this->file);
        $writer = new StreamingWriter($this->file);
        $writer->startSheet('Data')->appendRow([42]);
        self::assertFileDoesNotExist($this->file);
        $writer->close();
        self::assertFileExists($this->file);
    }

    public function testNoSheetsDoesNotRemoveExistingFile(): void
    {
        $writer = new StreamingWriter($this->file);

        try {
            $writer->close();
            self::fail('Expected close without sheets to fail.');
        } catch (WriterException $e) {
            self::assertStringContainsString('no sheets', $e->getMessage());
        }
        self::assertSame('previous workbook', file_get_contents($this->file));
    }

    public function testOutputStreamStillProducesAnArchive(): void
    {
        $writer = new StreamingWriter('php://output');
        $writer->startSheet('Data')->appendRow([42]);
        ob_start();

        try {
            $writer->close();
            $output = ob_get_contents();
        } finally {
            ob_end_clean();
        }
        self::assertIsString($output);
        self::assertStringStartsWith("PK\x03\x04", $output);
        file_put_contents($this->file, $output);
        $spreadsheet = (new Xlsx())->load($this->file);
        self::assertSame(42, $spreadsheet->getActiveSheet()->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testFailedReplacementRemovesTemporaryOutput(): void
    {
        $writer = new StreamingWriter($this->file);
        $writer->startSheet('Data')->appendRow([42]);
        $temporary = (new ReflectionProperty(StreamingWriter::class, 'temporaryFilename'))->getValue($writer);
        self::assertIsString($temporary);
        // A directory cannot be replaced with a regular file.
        (new ReflectionProperty(StreamingWriter::class, 'filename'))->setValue($writer, dirname($this->file));

        try {
            $writer->close();
            self::fail('Expected the replacement to fail.');
        } catch (WriterException $e) {
            self::assertStringContainsString('Failed to write the Xlsx file', $e->getMessage());
        }
        self::assertFileDoesNotExist($temporary);
        self::assertSame('previous workbook', file_get_contents($this->file));
    }
}
