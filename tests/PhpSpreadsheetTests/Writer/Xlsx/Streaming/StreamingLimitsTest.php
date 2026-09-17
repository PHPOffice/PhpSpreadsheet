<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingSheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class StreamingLimitsTest extends TestCase
{
    private string $file = '';

    protected function setUp(): void
    {
        $this->file = File::temporaryFilename();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->file)) {
            unlink($this->file);
        }
    }

    public function testLastRowAndColumnRoundTrip(): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Limits');
        (new ReflectionProperty(StreamingSheet::class, 'rowNumber'))->setValue($sheet, AddressRange::MAX_ROW - 1);
        $cells = array_fill(0, AddressRange::MAX_COLUMN_INT, null);
        $cells[AddressRange::MAX_COLUMN_INT - 1] = 42;
        $sheet->setColumnWidths([AddressRange::MAX_COLUMN_INT => 255.0]);
        $sheet->freezePane('XFD1048576');
        $sheet->appendRow($cells);
        $writer->close();

        $spreadsheet = (new Xlsx())->load($this->file);
        $loaded = $spreadsheet->getActiveSheet();
        self::assertSame(42, $loaded->getCell('XFD1048576')->getValue());
        self::assertSame(255.0, $loaded->getColumnDimension('XFD')->getWidth());
        self::assertSame('XFD1048576', $loaded->getFreezePane());
        $spreadsheet->disconnectWorksheets();
    }

    public function testTooManyRowsLeavesLastValidRowIntact(): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Limits');
        (new ReflectionProperty(StreamingSheet::class, 'rowNumber'))->setValue($sheet, AddressRange::MAX_ROW - 1);
        $sheet->appendRow([42]);

        try {
            $sheet->appendRow([43]);
            self::fail('Expected a row limit exception.');
        } catch (WriterException $e) {
            self::assertStringContainsString('1048576 rows', $e->getMessage());
        }
        $writer->close();
        $spreadsheet = (new Xlsx())->load($this->file);
        self::assertSame(42, $spreadsheet->getActiveSheet()->getCell('A1048576')->getValue());
        self::assertSame(1048576, $spreadsheet->getActiveSheet()->getHighestRow());
        $spreadsheet->disconnectWorksheets();
    }

    public function testTooManyColumnsLeavesSheetUsable(): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Limits');

        try {
            $sheet->appendRow(array_fill(0, AddressRange::MAX_COLUMN_INT + 1, null));
            self::fail('Expected a column limit exception.');
        } catch (WriterException $e) {
            self::assertStringContainsString('16384 columns', $e->getMessage());
        }
        $sheet->appendRow([42]);
        $writer->close();
        $spreadsheet = (new Xlsx())->load($this->file);
        self::assertSame(42, $spreadsheet->getActiveSheet()->getCell('A1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    #[DataProvider('invalidWidthProvider')]
    public function testInvalidWidthsLeaveExistingWidthsUnchanged(int $column, float $width): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Widths');
        $sheet->setColumnWidths([1 => 12.0]);

        try {
            $sheet->setColumnWidths([1 => 24.0, $column => $width]);
            self::fail('Expected invalid column widths to be rejected.');
        } catch (WriterException $e) {
            self::assertStringContainsString('invalid', $e->getMessage());
        }
        $writer->close();
        $spreadsheet = (new Xlsx())->load($this->file);
        self::assertSame(12.0, $spreadsheet->getActiveSheet()->getColumnDimension('A')->getWidth());
        $spreadsheet->disconnectWorksheets();
    }

    public static function invalidWidthProvider(): array
    {
        return [
            'column beyond XFD' => [16385, 12.0],
            'NaN' => [2, NAN],
            'positive infinity' => [2, INF],
            'negative infinity' => [2, -INF],
            'width above limit' => [2, 255.1],
        ];
    }

    public function testFreezePaneBeyondLastColumnThrows(): void
    {
        $writer = new StreamingWriter($this->file);
        $sheet = $writer->startSheet('Limits');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('beyond XFD');
        $sheet->freezePane('XFE1');
    }
}
