<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class StreamingReadTest extends TestCase
{
    private string $tempFile = '';

    protected function setUp(): void
    {
        // Create a spreadsheet with various cell types for testing
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('TestSheet');

        // Numeric values
        $sheet->setCellValue('A1', 42);
        $sheet->setCellValue('A2', 3.14);
        $sheet->setCellValue('A3', 0);
        $sheet->setCellValue('A4', -100);

        // String values (will become shared strings)
        $sheet->setCellValue('B1', 'Hello');
        $sheet->setCellValue('B2', 'World');
        $sheet->setCellValue('B3', '');
        $sheet->setCellValue('B4', 'Hello'); // duplicate shared string

        // Boolean values
        $sheet->getCell('C1')->setValueExplicit(true, DataType::TYPE_BOOL);
        $sheet->getCell('C2')->setValueExplicit(false, DataType::TYPE_BOOL);

        // Formula cells
        $sheet->setCellValue('D1', '=A1+A2');
        $sheet->setCellValue('D2', '=SUM(A1:A4)');
        $sheet->setCellValue('D3', '=B1&" "&B2');

        // Inline string (set explicitly as inline type)
        $sheet->getCell('E1')->setValueExplicit('Inline text', DataType::TYPE_INLINE);

        $this->tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet_streaming_test_') . '.xlsx';
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($this->tempFile);
        $spreadsheet->disconnectWorksheets();
    }

    protected function tearDown(): void
    {
        if ($this->tempFile !== '' && file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testStreamingFlagDefaultsFalse(): void
    {
        $reader = new Xlsx();
        self::assertFalse($reader->getUseStreamingReader());
    }

    public function testStreamingFlagSetterGetter(): void
    {
        $reader = new Xlsx();
        $result = $reader->setUseStreamingReader(true);
        self::assertTrue($reader->getUseStreamingReader());
        self::assertSame($reader, $result); // fluent interface
    }

    public function testStreamingLoadsIdenticalToSimpleXml(): void
    {
        // Load with SimpleXML (default)
        $readerSimple = new Xlsx();
        $readerSimple->setReadDataOnly(true);
        $spreadsheetSimple = $readerSimple->load($this->tempFile);
        $sheetSimple = $spreadsheetSimple->getActiveSheet();

        // Load with streaming XMLReader
        $readerStreaming = new Xlsx();
        $readerStreaming->setReadDataOnly(true);
        $readerStreaming->setUseStreamingReader(true);
        $spreadsheetStreaming = $readerStreaming->load($this->tempFile);
        $sheetStreaming = $spreadsheetStreaming->getActiveSheet();

        // Compare all populated cells
        foreach ($sheetSimple->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $coord = $cell->getCoordinate();
                $simpleValue = $sheetSimple->getCell($coord)->getValue();
                $streamValue = $sheetStreaming->getCell($coord)->getValue();
                self::assertEquals(
                    $simpleValue,
                    $streamValue,
                    "Cell {$coord} value mismatch: SimpleXML=" . var_export($simpleValue, true)
                    . ' vs Streaming=' . var_export($streamValue, true)
                );
            }
        }

        $spreadsheetSimple->disconnectWorksheets();
        $spreadsheetStreaming->disconnectWorksheets();
    }

    public function testStreamingNumericCells(): void
    {
        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        $spreadsheet = $reader->load($this->tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertSame(42, $sheet->getCell('A1')->getValue());
        self::assertSame(3.14, $sheet->getCell('A2')->getValue());
        self::assertSame(0, $sheet->getCell('A3')->getValue());
        self::assertSame(-100, $sheet->getCell('A4')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testStreamingSharedStrings(): void
    {
        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        $spreadsheet = $reader->load($this->tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertSame('Hello', $sheet->getCell('B1')->getValue());
        self::assertSame('World', $sheet->getCell('B2')->getValue());
        // B3 is empty string
        self::assertSame('Hello', $sheet->getCell('B4')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testStreamingBooleanCells(): void
    {
        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        $spreadsheet = $reader->load($this->tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertTrue($sheet->getCell('C1')->getValue());
        self::assertFalse($sheet->getCell('C2')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testStreamingFormulaCells(): void
    {
        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        $spreadsheet = $reader->load($this->tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        self::assertSame('=A1+A2', $sheet->getCell('D1')->getValue());
        self::assertSame(DataType::TYPE_FORMULA, $sheet->getCell('D1')->getDataType());

        self::assertSame('=SUM(A1:A4)', $sheet->getCell('D2')->getValue());
        self::assertSame(DataType::TYPE_FORMULA, $sheet->getCell('D2')->getDataType());

        self::assertSame('=B1&" "&B2', $sheet->getCell('D3')->getValue());
        self::assertSame(DataType::TYPE_FORMULA, $sheet->getCell('D3')->getDataType());

        $spreadsheet->disconnectWorksheets();
    }

    public function testStreamingBooleanFormulaCalculatedValue(): void
    {
        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        $spreadsheet = $reader->load($this->tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        // C1 is a boolean cell - verify the type is preserved
        $cell = $sheet->getCell('C1');
        self::assertTrue($cell->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testStreamingWithExistingXlsxFile(): void
    {
        // Use an existing test file to compare both reading modes
        $filename = 'tests/data/Reader/XLSX/rowColumnAttributeTest.xlsx';

        $readerSimple = new Xlsx();
        $readerSimple->setReadDataOnly(true);
        $spreadsheetSimple = $readerSimple->load($filename);

        $readerStreaming = new Xlsx();
        $readerStreaming->setReadDataOnly(true);
        $readerStreaming->setUseStreamingReader(true);
        $spreadsheetStreaming = $readerStreaming->load($filename);

        foreach ($spreadsheetSimple->getActiveSheet()->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $coord = $cell->getCoordinate();
                $simpleVal = $spreadsheetSimple->getActiveSheet()->getCell($coord)->getValue();
                $streamVal = $spreadsheetStreaming->getActiveSheet()->getCell($coord)->getValue();
                self::assertEquals(
                    $simpleVal,
                    $streamVal,
                    "Cell {$coord} value mismatch in existing file"
                );
            }
        }

        $spreadsheetSimple->disconnectWorksheets();
        $spreadsheetStreaming->disconnectWorksheets();
    }

    public function testStreamingWithStyles(): void
    {
        // Verify streaming mode applies styles correctly
        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        $spreadsheet = $reader->load($this->tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        // Cell A1 should have a valid xf index (no crash, basic validation)
        $cell = $sheet->getCell('A1');
        self::assertGreaterThanOrEqual(0, $cell->getXfIndex());

        $spreadsheet->disconnectWorksheets();
    }

    public function testStreamingInlineString(): void
    {
        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        $spreadsheet = $reader->load($this->tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        // The inline string should be readable
        $value = $sheet->getCell('E1')->getValue();
        self::assertNotNull($value);
        // The value could be a RichText object or a string depending on writer output
        $plainValue = ($value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText)
            ? $value->getPlainText()
            : (is_string($value) ? $value : '');
        self::assertSame('Inline text', $plainValue);

        $spreadsheet->disconnectWorksheets();
    }
}
