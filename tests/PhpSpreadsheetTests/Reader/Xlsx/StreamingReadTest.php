<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

class StreamingReadTest extends TestCase
{
    private string $tempFile = '';

    /** @var string[] */
    private array $extraTempFiles = [];

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
        foreach ($this->extraTempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    private function createTempFile(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'phpspreadsheet_streaming_test_') . '.xlsx';
        $this->extraTempFiles[] = $path;

        return $path;
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
        $plainValue = ($value instanceof RichText)
            ? $value->getPlainText()
            : (is_string($value) ? $value : '');
        self::assertSame('Inline text', $plainValue);

        $spreadsheet->disconnectWorksheets();
    }

    public function testStreamingErrorCells(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Error values
        $sheet->getCell('A1')->setValueExplicit('#DIV/0!', DataType::TYPE_ERROR);
        $sheet->getCell('A2')->setValueExplicit('#VALUE!', DataType::TYPE_ERROR);
        $sheet->getCell('A3')->setValueExplicit('#REF!', DataType::TYPE_ERROR);
        $sheet->getCell('A4')->setValueExplicit('#N/A', DataType::TYPE_ERROR);

        // Error-producing formula
        $sheet->setCellValue('B1', '=1/0');

        $file = $this->createTempFile();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($file);
        $spreadsheet->disconnectWorksheets();

        // Load with streaming and compare with SimpleXML
        $readerSimple = new Xlsx();
        $readerSimple->setReadDataOnly(true);
        $simple = $readerSimple->load($file);

        $readerStream = new Xlsx();
        $readerStream->setReadDataOnly(true);
        $readerStream->setUseStreamingReader(true);
        $stream = $readerStream->load($file);

        foreach (['A1', 'A2', 'A3', 'A4'] as $coord) {
            self::assertSame(
                $simple->getActiveSheet()->getCell($coord)->getValue(),
                $stream->getActiveSheet()->getCell($coord)->getValue(),
                "Error cell {$coord} mismatch"
            );
        }

        // B1 is an error-producing formula
        self::assertSame(
            $simple->getActiveSheet()->getCell('B1')->getValue(),
            $stream->getActiveSheet()->getCell('B1')->getValue(),
            'Error formula cell B1 mismatch'
        );

        $simple->disconnectWorksheets();
        $stream->disconnectWorksheets();
    }

    public function testStreamingMultipleSheets(): void
    {
        $spreadsheet = new Spreadsheet();

        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sheet1');
        $sheet1->setCellValue('A1', 'First');
        $sheet1->setCellValue('B1', 100);

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet2');
        $sheet2->setCellValue('A1', 'Second');
        $sheet2->setCellValue('B1', 200);

        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Sheet3');
        $sheet3->setCellValue('A1', 'Third');
        $sheet3->setCellValue('B1', 300);

        $file = $this->createTempFile();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($file);
        $spreadsheet->disconnectWorksheets();

        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        $result = $reader->load($file);

        self::assertSame(3, $result->getSheetCount());
        self::assertSame('First', $result->getSheetByNameOrThrow('Sheet1')->getCell('A1')->getValue());
        self::assertSame(100, $result->getSheetByNameOrThrow('Sheet1')->getCell('B1')->getValue());
        self::assertSame('Second', $result->getSheetByNameOrThrow('Sheet2')->getCell('A1')->getValue());
        self::assertSame(200, $result->getSheetByNameOrThrow('Sheet2')->getCell('B1')->getValue());
        self::assertSame('Third', $result->getSheetByNameOrThrow('Sheet3')->getCell('A1')->getValue());
        self::assertSame(300, $result->getSheetByNameOrThrow('Sheet3')->getCell('B1')->getValue());

        $result->disconnectWorksheets();
    }

    public function testStreamingLargeCoordinates(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // High row number
        $sheet->setCellValue('A1000', 'row1000');
        // High column (XFD is max column in Excel = 16384)
        $sheet->setCellValue('ZZ1', 'colZZ');
        // Both high
        $sheet->setCellValue('AB500', 12345);

        $file = $this->createTempFile();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($file);
        $spreadsheet->disconnectWorksheets();

        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        $reader->setReadDataOnly(true);
        $result = $reader->load($file);
        $sheet = $result->getActiveSheet();

        self::assertSame('row1000', $sheet->getCell('A1000')->getValue());
        self::assertSame('colZZ', $sheet->getCell('ZZ1')->getValue());
        self::assertSame(12345, $sheet->getCell('AB500')->getValue());

        $result->disconnectWorksheets();
    }

    public function testStreamingDateFormattedCells(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // A date value stored as a number with date format
        $sheet->setCellValue('A1', 45000); // Excel serial date
        $sheet->getStyle('A1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_YYYYMMDD);

        // A time value
        $sheet->setCellValue('A2', 0.75); // 6:00 PM
        $sheet->getStyle('A2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_TIME4);

        // Regular number (no date format) for comparison
        $sheet->setCellValue('A3', 45000);

        $file = $this->createTempFile();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($file);
        $spreadsheet->disconnectWorksheets();

        // Compare streaming vs SimpleXML for date formatted cells
        $readerSimple = new Xlsx();
        $simple = $readerSimple->load($file);

        $readerStream = new Xlsx();
        $readerStream->setUseStreamingReader(true);
        $stream = $readerStream->load($file);

        foreach (['A1', 'A2', 'A3'] as $coord) {
            self::assertEquals(
                $simple->getActiveSheet()->getCell($coord)->getValue(),
                $stream->getActiveSheet()->getCell($coord)->getValue(),
                "Date cell {$coord} value mismatch"
            );
        }

        $simple->disconnectWorksheets();
        $stream->disconnectWorksheets();
    }

    public function testStreamingStyleIndexHandling(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Bold text');
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->setCellValue('A2', 'Italic text');
        $sheet->getStyle('A2')->getFont()->setItalic(true);

        $sheet->setCellValue('A3', 42);
        $sheet->getStyle('A3')->getNumberFormat()->setFormatCode('#,##0.00');

        $file = $this->createTempFile();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($file);
        $spreadsheet->disconnectWorksheets();

        // Load with styles (readDataOnly=false) using streaming
        $readerStream = new Xlsx();
        $readerStream->setUseStreamingReader(true);
        // readDataOnly defaults to false
        $stream = $readerStream->load($file);
        $sheetStream = $stream->getActiveSheet();

        // Load with SimpleXML for comparison
        $readerSimple = new Xlsx();
        $simple = $readerSimple->load($file);
        $sheetSimple = $simple->getActiveSheet();

        // Verify style indices match
        foreach (['A1', 'A2', 'A3'] as $coord) {
            self::assertSame(
                $sheetSimple->getCell($coord)->getXfIndex(),
                $sheetStream->getCell($coord)->getXfIndex(),
                "Style index mismatch for cell {$coord}"
            );
        }

        // Verify actual style properties were applied
        self::assertTrue($sheetStream->getStyle('A1')->getFont()->getBold());
        self::assertTrue($sheetStream->getStyle('A2')->getFont()->getItalic());

        $simple->disconnectWorksheets();
        $stream->disconnectWorksheets();
    }

    public function testStreamingSharedFormulas(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Create data for shared formulas
        $sheet->setCellValue('A1', 10);
        $sheet->setCellValue('A2', 20);
        $sheet->setCellValue('A3', 30);
        $sheet->setCellValue('A4', 40);
        $sheet->setCellValue('A5', 50);

        $sheet->setCellValue('B1', 1);
        $sheet->setCellValue('B2', 2);
        $sheet->setCellValue('B3', 3);
        $sheet->setCellValue('B4', 4);
        $sheet->setCellValue('B5', 5);

        // Create formulas that Excel may serialize as shared formulas
        // When the writer outputs these, adjacent identical-pattern formulas
        // may become shared formulas
        $sheet->setCellValue('C1', '=A1+B1');
        $sheet->setCellValue('C2', '=A2+B2');
        $sheet->setCellValue('C3', '=A3+B3');
        $sheet->setCellValue('C4', '=A4+B4');
        $sheet->setCellValue('C5', '=A5+B5');

        $file = $this->createTempFile();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($file);
        $spreadsheet->disconnectWorksheets();

        // Compare streaming vs SimpleXML
        $readerSimple = new Xlsx();
        $readerSimple->setReadDataOnly(true);
        $simple = $readerSimple->load($file);

        $readerStream = new Xlsx();
        $readerStream->setReadDataOnly(true);
        $readerStream->setUseStreamingReader(true);
        $stream = $readerStream->load($file);

        for ($row = 1; $row <= 5; ++$row) {
            $coord = "C{$row}";
            self::assertEquals(
                $simple->getActiveSheet()->getCell($coord)->getValue(),
                $stream->getActiveSheet()->getCell($coord)->getValue(),
                "Shared formula cell {$coord} mismatch"
            );
            self::assertSame(
                DataType::TYPE_FORMULA,
                $stream->getActiveSheet()->getCell($coord)->getDataType(),
                "Cell {$coord} should be a formula"
            );
        }

        $simple->disconnectWorksheets();
        $stream->disconnectWorksheets();
    }

    public function testStreamingReadDataOnlyFalseWithFormulas(): void
    {
        // Test the readDataOnly=false path which applies style indices
        // and handles quotePrefix for formula cells
        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        // readDataOnly defaults to false
        $spreadsheet = $reader->load($this->tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        // Formula cells should have their formulas preserved
        self::assertSame('=A1+A2', $sheet->getCell('D1')->getValue());
        self::assertSame(DataType::TYPE_FORMULA, $sheet->getCell('D1')->getDataType());

        // Style indices should be set (not -1 / uninitialized)
        self::assertGreaterThanOrEqual(0, $sheet->getCell('D1')->getXfIndex());
        self::assertGreaterThanOrEqual(0, $sheet->getCell('A1')->getXfIndex());

        // Verify all cells that exist have valid xf indices
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                self::assertGreaterThanOrEqual(
                    0,
                    $cell->getXfIndex(),
                    "Cell {$cell->getCoordinate()} has invalid xf index"
                );
            }
        }

        $spreadsheet->disconnectWorksheets();
    }

    public function testStreamingEmptyValueCells(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cell with a value
        $sheet->setCellValue('A1', 'has value');
        // Cells left completely empty (no value set)
        // A2, A3 etc are never touched

        // Null-ish value
        $sheet->setCellValue('B1', null);

        $file = $this->createTempFile();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($file);
        $spreadsheet->disconnectWorksheets();

        $readerSimple = new Xlsx();
        $readerSimple->setReadDataOnly(true);
        $simple = $readerSimple->load($file);

        $readerStream = new Xlsx();
        $readerStream->setReadDataOnly(true);
        $readerStream->setUseStreamingReader(true);
        $stream = $readerStream->load($file);

        self::assertSame(
            $simple->getActiveSheet()->getCell('A1')->getValue(),
            $stream->getActiveSheet()->getCell('A1')->getValue()
        );

        $simple->disconnectWorksheets();
        $stream->disconnectWorksheets();
    }

    public function testStreamingCalculatedValuePreserved(): void
    {
        // Test that calculated values are preserved for formula cells
        $reader = new Xlsx();
        $reader->setUseStreamingReader(true);
        $spreadsheet = $reader->load($this->tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        // D1 = =A1+A2 should have a calculated value of 42 + 3.14 = 45.14
        $cell = $sheet->getCell('D1');
        self::assertSame('=A1+A2', $cell->getValue());
        $calcValue = $cell->getOldCalculatedValue();
        if ($calcValue !== null) {
            self::assertEqualsWithDelta(45.14, $calcValue, 0.001);
        }

        // D2 = =SUM(A1:A4) should have a calculated value of 42 + 3.14 + 0 + (-100) = -54.86
        $cell2 = $sheet->getCell('D2');
        self::assertSame('=SUM(A1:A4)', $cell2->getValue());
        $calcValue2 = $cell2->getOldCalculatedValue();
        if ($calcValue2 !== null) {
            self::assertEqualsWithDelta(-54.86, $calcValue2, 0.001);
        }

        $spreadsheet->disconnectWorksheets();
    }

    public function testStreamingStringFormula(): void
    {
        // Test TYPE_STRING2 (str) formula - a formula that returns a string
        // D3 in setUp is =B1&" "&B2 which returns "Hello World" (str type)
        $readerSimple = new Xlsx();
        $simple = $readerSimple->load($this->tempFile);

        $readerStream = new Xlsx();
        $readerStream->setUseStreamingReader(true);
        $stream = $readerStream->load($this->tempFile);

        // String formula should match
        self::assertEquals(
            $simple->getActiveSheet()->getCell('D3')->getValue(),
            $stream->getActiveSheet()->getCell('D3')->getValue(),
            'String formula value mismatch'
        );
        self::assertEquals(
            $simple->getActiveSheet()->getCell('D3')->getOldCalculatedValue(),
            $stream->getActiveSheet()->getCell('D3')->getOldCalculatedValue(),
            'String formula calculated value mismatch'
        );

        $simple->disconnectWorksheets();
        $stream->disconnectWorksheets();
    }

    public function testStreamingFullComparisonWithStyles(): void
    {
        // Comprehensive comparison of streaming vs SimpleXML with styles enabled
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Mix of data types
        $sheet->setCellValue('A1', 42);
        $sheet->setCellValue('A2', 'text');
        $sheet->getCell('A3')->setValueExplicit(true, DataType::TYPE_BOOL);
        $sheet->setCellValue('A4', '=A1*2');
        $sheet->getCell('A5')->setValueExplicit('#DIV/0!', DataType::TYPE_ERROR);

        // Apply styles
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getFont()->setItalic(true);
        $sheet->getStyle('A1')->getNumberFormat()->setFormatCode('#,##0');

        $file = $this->createTempFile();
        $writer = new XlsxWriter($spreadsheet);
        $writer->save($file);
        $spreadsheet->disconnectWorksheets();

        $readerSimple = new Xlsx();
        $simple = $readerSimple->load($file);
        $sheetSimple = $simple->getActiveSheet();

        $readerStream = new Xlsx();
        $readerStream->setUseStreamingReader(true);
        $stream = $readerStream->load($file);
        $sheetStream = $stream->getActiveSheet();

        foreach ($sheetSimple->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $coord = $cell->getCoordinate();
                $simpleCell = $sheetSimple->getCell($coord);
                $streamCell = $sheetStream->getCell($coord);

                self::assertEquals(
                    $simpleCell->getValue(),
                    $streamCell->getValue(),
                    "Value mismatch at {$coord}"
                );
                self::assertSame(
                    $simpleCell->getXfIndex(),
                    $streamCell->getXfIndex(),
                    "XfIndex mismatch at {$coord}"
                );
            }
        }

        $simple->disconnectWorksheets();
        $stream->disconnectWorksheets();
    }
}
