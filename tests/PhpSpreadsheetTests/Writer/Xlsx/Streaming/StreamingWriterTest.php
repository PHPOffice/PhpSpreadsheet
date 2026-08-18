<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx\Streaming;

use DateTimeImmutable;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Exception as WriterException;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamedCell;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Streaming\StreamingWriter;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Stringable;
use ZipArchive;

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

    /** Cell values read back as RichText or scalar; normalize to string for comparison. */
    private static function stringValue(mixed $value): string
    {
        if (is_scalar($value) || $value instanceof Stringable) {
            return (string) $value;
        }

        throw new RuntimeException('Expected a stringable cell value, got ' . get_debug_type($value) . '.');
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
        self::assertSame('Name', self::stringValue($worksheet->getCell('A1')->getValue()));
        self::assertSame('Ärger & <Freude>', self::stringValue($worksheet->getCell('A2')->getValue()));
        self::assertSame(42, $worksheet->getCell('B2')->getValue());
        self::assertSame(1.25, $worksheet->getCell('C2')->getValue());
        self::assertTrue($worksheet->getCell('D2')->getValue());
        self::assertFalse($worksheet->getCell('D3')->getValue());
        self::assertNull($worksheet->getCell('A3')->getValue());
        self::assertSame('  padded  ', self::stringValue($worksheet->getCell('C3')->getValue()));
    }

    public function testUnsupportedValueThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $sheet->appendRow([new stdClass()]);
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
        self::assertSame('=not a formula', self::stringValue($worksheet->getCell('A3')->getValue()));
    }

    public function testDateTimeRoundTrip(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow([new DateTimeImmutable('2026-01-02 03:04:05')]);
        $writer->close();

        $worksheet = (new XlsxReader())->load($file)->getSheetByNameOrThrow('Data');
        $cell = $worksheet->getCell('A1');
        self::assertEqualsWithDelta(46024.12783564815, $cell->getValue(), 1E-8);
        self::assertSame(
            NumberFormat::FORMAT_DATE_DATETIME,
            $worksheet->getStyle('A1')->getNumberFormat()->getFormatCode()
        );
    }

    public function testStylesRoundTrip(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $bold = $writer->registerStyle(['font' => ['bold' => true]]);
        $money = $writer->registerStyle(['numberFormat' => ['formatCode' => '#,##0.00']]);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow(['Header A', 'Header B'], $bold);
        $sheet->appendRow([new StreamedCell(1234.5, $money), 'plain']);
        $writer->close();

        $worksheet = (new XlsxReader())->load($file)->getSheetByNameOrThrow('Data');
        self::assertTrue($worksheet->getStyle('A1')->getFont()->getBold());
        self::assertTrue($worksheet->getStyle('B1')->getFont()->getBold());
        self::assertSame('#,##0.00', $worksheet->getStyle('A2')->getNumberFormat()->getFormatCode());
        self::assertFalse($worksheet->getStyle('B2')->getFont()->getBold());
    }

    public function testUnregisteredStyleIdThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('has not been registered');
        $sheet->appendRow(['x'], 99);
    }

    public function testSheetFeaturesRoundTrip(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $sheet->setColumnWidths([1 => 25.5, 3 => 8.0]);
        $sheet->freezePane('A2');
        $sheet->setAutoFilterToWrittenRange();
        $sheet->appendRow(['H1', 'H2', 'H3']);
        $sheet->appendRow(['a', 'b', 'c']);
        $writer->close();

        $worksheet = (new XlsxReader())->load($file)->getSheetByNameOrThrow('Data');
        self::assertSame(25.5, $worksheet->getColumnDimension('A')->getWidth());
        self::assertSame(8.0, $worksheet->getColumnDimension('C')->getWidth());
        self::assertSame('A2', $worksheet->getFreezePane());
        self::assertSame('A1:C2', $worksheet->getAutoFilter()->getRange());
    }

    public function testCalcPrReflectsFormulaPresence(): void
    {
        $noFormulaFile = $this->tempFile();
        $writer = new StreamingWriter($noFormulaFile);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow([1, 2]);
        $writer->close();

        $formulaFile = $this->tempFile();
        $writer = new StreamingWriter($formulaFile);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow([1, 2]);
        $sheet->appendRow(['=SUM(A1:B1)']);
        $writer->close();

        $noFormulaCalcPr = $this->readCalcPr($noFormulaFile);
        $formulaCalcPr = $this->readCalcPr($formulaFile);

        self::assertSame('0', $noFormulaCalcPr['fullCalcOnLoad']);
        self::assertSame('1', $noFormulaCalcPr['calcCompleted']);
        self::assertSame('0', $noFormulaCalcPr['forceFullCalc']);

        self::assertSame('1', $formulaCalcPr['fullCalcOnLoad']);
        self::assertSame('0', $formulaCalcPr['calcCompleted']);
        self::assertSame('1', $formulaCalcPr['forceFullCalc']);
    }

    /** @return array<string, string> */
    private function readCalcPr(string $file): array
    {
        $zip = new ZipArchive();
        $zip->open($file);
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $zip->close();
        self::assertIsString($workbookXml);

        self::assertMatchesRegularExpression('/<calcPr\b[^>]*\/>/', $workbookXml);
        preg_match('/<calcPr\b([^>]*)\/>/', $workbookXml, $matches);
        $attributesXml = $matches[1] ?? '';
        preg_match_all('/(\w+)="([^"]*)"/', $attributesXml, $attributeMatches, \PREG_SET_ORDER);
        $attributes = [];
        foreach ($attributeMatches as $attributeMatch) {
            $attributes[$attributeMatch[1]] = $attributeMatch[2];
        }

        return $attributes;
    }

    public function testMultipleSheetsWithDataRoundTrip(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $first = $writer->startSheet('First');
        $first->appendRow(['first-a1', 'first-b1']);
        $first->appendRow(['first-a2', 'first-b2']);
        $second = $writer->startSheet('Second');
        $second->appendRow(['second-a1', 'second-b1']);
        $writer->close();

        $spreadsheet = (new XlsxReader())->load($file);
        self::assertSame(['First', 'Second'], $spreadsheet->getSheetNames());

        $firstSheet = $spreadsheet->getSheetByNameOrThrow('First');
        self::assertSame('first-a1', self::stringValue($firstSheet->getCell('A1')->getValue()));
        self::assertSame('first-b1', self::stringValue($firstSheet->getCell('B1')->getValue()));
        self::assertSame('first-a2', self::stringValue($firstSheet->getCell('A2')->getValue()));
        self::assertSame('first-b2', self::stringValue($firstSheet->getCell('B2')->getValue()));

        $secondSheet = $spreadsheet->getSheetByNameOrThrow('Second');
        self::assertSame('second-a1', self::stringValue($secondSheet->getCell('A1')->getValue()));
        self::assertSame('second-b1', self::stringValue($secondSheet->getCell('B1')->getValue()));
        self::assertNull($secondSheet->getCell('A2')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    public function testColumnWidthsAfterFirstRowThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow(['x']);
        $this->expectException(WriterException::class);
        $sheet->setColumnWidths([1 => 10.0]);
    }

    public function testFreezePaneAfterFirstRowThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $sheet->appendRow(['x']);
        $this->expectException(WriterException::class);
        $sheet->freezePane('A1');
    }

    public function testColumnWidthsRejectsInvalidColumnNumber(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('is invalid; column numbers are 1-based');
        $sheet->setColumnWidths([0 => 10.0]);
    }

    public function testColumnWidthsRejectsNonPositiveWidth(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('is invalid; width must be positive');
        $sheet->setColumnWidths([1 => 0.0]);
    }

    public function testUnsupportedStreamedCellDataTypeThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('Unsupported StreamedCell data type');
        $sheet->appendRow([new StreamedCell(42, null, DataType::TYPE_NUMERIC)]);
    }

    public function testNanThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('not a finite number');
        $sheet->appendRow([NAN]);
    }

    public function testInfinityThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('not a finite number');
        $sheet->appendRow([INF]);
    }

    public function testInvalidUtf8StringThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('not valid UTF-8');
        $sheet->appendRow(["bad \xC3\x28 utf8"]);
    }

    public function testInvalidUtf8FormulaThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('not valid UTF-8');
        $sheet->appendRow(["=\"bad \xC3\x28\""]);
    }

    public function testStringOverExcelLimitThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('32767');
        $sheet->appendRow([str_repeat('x', DataType::MAX_STRING_LENGTH + 1)]);
    }

    public function testStringAtExcelLimitRoundTrips(): void
    {
        $file = $this->tempFile();
        $value = str_repeat('x', DataType::MAX_STRING_LENGTH);
        $writer = new StreamingWriter($file);
        $writer->startSheet('Data')->appendRow([$value]);
        $writer->close();

        $worksheet = (new XlsxReader())->load($file)->getSheetByNameOrThrow('Data');
        self::assertSame($value, (string) $worksheet->getCell('A1')->getValue());
    }

    public function testInvalidUtf8ForcedStringThrows(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $this->expectException(WriterException::class);
        $this->expectExceptionMessage('not valid UTF-8');
        $sheet->appendRow([new StreamedCell("bad \xC3\x28 utf8", null, DataType::TYPE_STRING)]);
    }

    public function testStreamedCellWithNullValueLeavesCellEmpty(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $writer->startSheet('Data')->appendRow([new StreamedCell(null), 'b']);
        $writer->close();

        $worksheet = (new XlsxReader())->load($file)->getSheetByNameOrThrow('Data');
        self::assertNull($worksheet->getCell('A1')->getValue());
        self::assertSame('b', (string) $worksheet->getCell('B1')->getValue());
    }

    public function testFreezePaneA1IsANoOp(): void
    {
        $file = $this->tempFile();
        $writer = new StreamingWriter($file);
        $sheet = $writer->startSheet('Data');
        $sheet->freezePane('A1');
        $sheet->appendRow(['x']);
        $writer->close();

        $worksheet = (new XlsxReader())->load($file)->getSheetByNameOrThrow('Data');
        self::assertNull($worksheet->getFreezePane());
    }
}
