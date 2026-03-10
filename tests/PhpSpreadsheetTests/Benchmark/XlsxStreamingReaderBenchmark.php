<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Benchmark;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('benchmark')]
class XlsxStreamingReaderBenchmark extends TestCase
{
    private const ROW_COUNT = 2000;

    private const COL_COUNT = 15;

    private string $tempFile = '';

    protected function setUp(): void
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet_bench_') . '.xlsx';
        $this->generateTestFile();
    }

    protected function tearDown(): void
    {
        if ($this->tempFile !== '' && file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    /**
     * Generate a substantial XLSX file with mixed data types.
     *
     * Layout per row across 15 columns:
     *  A-E: integers / floats
     *  F-J: strings
     *  K-L: formulas
     *  M:   boolean true
     *  N:   boolean false
     *  O:   inline string
     */
    private function generateTestFile(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('BenchmarkData');

        for ($row = 1; $row <= self::ROW_COUNT; ++$row) {
            // Numeric columns A-E
            $sheet->setCellValue("A{$row}", $row);
            $sheet->setCellValue("B{$row}", $row * 1.5);
            $sheet->setCellValue("C{$row}", $row + 100);
            $sheet->setCellValue("D{$row}", $row * -0.25);
            $sheet->setCellValue("E{$row}", 0);

            // String columns F-J
            $sheet->setCellValue("F{$row}", "Name_{$row}");
            $sheet->setCellValue("G{$row}", "Description for row {$row}");
            $sheet->setCellValue("H{$row}", 'Category_' . ($row % 50));
            $sheet->setCellValue("I{$row}", "Ref-{$row}-" . str_pad((string) $row, 6, '0', STR_PAD_LEFT));
            $sheet->setCellValue("J{$row}", ($row % 2 === 0) ? 'Even' : 'Odd');

            // Formula columns K-L
            $sheet->setCellValue("K{$row}", "=A{$row}+C{$row}");
            $sheet->setCellValue("L{$row}", "=B{$row}*2");

            // Boolean columns M-N
            $sheet->getCell("M{$row}")->setValueExplicit(true, DataType::TYPE_BOOL);
            $sheet->getCell("N{$row}")->setValueExplicit(false, DataType::TYPE_BOOL);

            // Inline string column O
            $sheet->getCell("O{$row}")->setValueExplicit("Inline_{$row}", DataType::TYPE_INLINE);
        }

        $writer = new XlsxWriter($spreadsheet);
        $writer->save($this->tempFile);
        $spreadsheet->disconnectWorksheets();
    }

    public function testStreamingReaderPerformanceAndCorrectness(): void
    {
        // --- SimpleXML (default) ---
        gc_collect_cycles();
        $peakBefore = memory_get_peak_usage(true);

        $startSimple = hrtime(true);
        $readerSimple = new Xlsx();
        $readerSimple->setReadDataOnly(true);
        $readerSimple->setUseStreamingReader(false);
        $spreadsheetSimple = $readerSimple->load($this->tempFile);
        $elapsedSimple = (hrtime(true) - $startSimple) / 1e6; // ms
        $peakSimple = memory_get_peak_usage(true);
        $memSimple = $peakSimple - $peakBefore;

        // Extract reference data from SimpleXML load for comparison
        $sheetSimple = $spreadsheetSimple->getActiveSheet();
        $simpleValues = [];
        foreach ($sheetSimple->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $coord = $cell->getCoordinate();
                $simpleValues[$coord] = $sheetSimple->getCell($coord)->getValue();
            }
        }
        $spreadsheetSimple->disconnectWorksheets();
        unset($spreadsheetSimple, $readerSimple, $sheetSimple);

        // --- XMLReader streaming ---
        gc_collect_cycles();
        $peakBeforeStreaming = memory_get_peak_usage(true);

        $startStreaming = hrtime(true);
        $readerStreaming = new Xlsx();
        $readerStreaming->setReadDataOnly(true);
        $readerStreaming->setUseStreamingReader(true);
        $spreadsheetStreaming = $readerStreaming->load($this->tempFile);
        $elapsedStreaming = (hrtime(true) - $startStreaming) / 1e6; // ms
        $peakStreaming = memory_get_peak_usage(true);
        $memStreaming = $peakStreaming - $peakBeforeStreaming;

        // --- Verify data matches between both reading modes ---
        $sheetStreaming = $spreadsheetStreaming->getActiveSheet();
        $mismatchCount = 0;
        foreach ($simpleValues as $coord => $expectedValue) {
            $streamingValue = $sheetStreaming->getCell($coord)->getValue();

            // Normalize inline strings (may come back as RichText)
            $expected = $expectedValue;
            $actual = $streamingValue;
            if ($expected instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                $expected = $expected->getPlainText();
            }
            if ($actual instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                $actual = $actual->getPlainText();
            }

            if ($expected != $actual) {
                ++$mismatchCount;
                if ($mismatchCount <= 5) {
                    fwrite(STDERR, sprintf(
                        "  MISMATCH at %s: SimpleXML=%s, Streaming=%s\n",
                        $coord,
                        var_export($expectedValue, true),
                        var_export($streamingValue, true)
                    ));
                }
            }
        }

        $spreadsheetStreaming->disconnectWorksheets();
        unset($spreadsheetStreaming, $readerStreaming, $sheetStreaming);

        // --- Output benchmark results ---
        $fileSize = filesize($this->tempFile);
        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== XLSX Streaming Reader Benchmark ===\n");
        fwrite(STDERR, sprintf("  File: %s rows x %s cols (%s KB)\n", self::ROW_COUNT, self::COL_COUNT, round(($fileSize ?: 0) / 1024, 1)));
        fwrite(STDERR, sprintf("  SimpleXML time:  %8.1f ms\n", $elapsedSimple));
        fwrite(STDERR, sprintf("  Streaming time:  %8.1f ms\n", $elapsedStreaming));
        fwrite(STDERR, sprintf("  SimpleXML peak memory delta:  %8.2f MB\n", $memSimple / 1024 / 1024));
        fwrite(STDERR, sprintf("  Streaming peak memory delta:  %8.2f MB\n", $memStreaming / 1024 / 1024));
        if ($elapsedSimple > 0) {
            $speedup = $elapsedSimple / $elapsedStreaming;
            fwrite(STDERR, sprintf("  Speed ratio (SimpleXML / Streaming): %.2fx\n", $speedup));
        }
        if ($memSimple > 0) {
            $memRatio = $memSimple / max($memStreaming, 1);
            fwrite(STDERR, sprintf("  Memory ratio (SimpleXML / Streaming): %.2fx\n", $memRatio));
        }
        fwrite(STDERR, sprintf("  Cell values compared: %d\n", count($simpleValues)));
        fwrite(STDERR, sprintf("  Mismatches: %d\n", $mismatchCount));
        fwrite(STDERR, "=======================================\n\n");

        // Assertions
        self::assertSame(0, $mismatchCount, "All cell values must match between SimpleXML and streaming reader");
        self::assertCount(self::ROW_COUNT * self::COL_COUNT, $simpleValues, "Expected all cells to be populated");
    }
}
