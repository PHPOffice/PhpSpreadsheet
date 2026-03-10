<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Benchmark;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;

/**
 * @group benchmark
 */
class XlsxZipReadCacheBenchmark extends TestCase
{
    private string $tempFile = '';

    protected function setUp(): void
    {
        $this->tempFile = File::temporaryFilename();
        $this->createComplexTestFile($this->tempFile);
    }

    protected function tearDown(): void
    {
        if ($this->tempFile !== '' && file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    /**
     * Create a test XLSX file with multiple sheets, styles, and formulas
     * to exercise repeated zip entry reads during loading.
     */
    private function createComplexTestFile(string $path): void
    {
        $spreadsheet = new Spreadsheet();

        // Sheet 1: Data with styles and formulas
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('SalesData');

        // Header row with bold styling
        $headers = ['ID', 'Product', 'Category', 'Price', 'Quantity', 'Subtotal', 'Tax', 'Total'];
        foreach ($headers as $col => $header) {
            $colLetter = Coordinate::stringFromColumnIndex($col + 1);
            $sheet1->setCellValue("{$colLetter}1", $header);
            $sheet1->getStyle("{$colLetter}1")->getFont()->setBold(true);
            $sheet1->getStyle("{$colLetter}1")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4472C4');
            $sheet1->getStyle("{$colLetter}1")->getFont()->getColor()->setARGB('FFFFFFFF');
        }

        // Populate 100 rows of data with formulas
        $categories = ['Electronics', 'Books', 'Clothing', 'Food', 'Sports'];
        $products = ['Widget', 'Gadget', 'Doohickey', 'Thingamajig', 'Whatchamacallit'];
        for ($row = 2; $row <= 101; ++$row) {
            $sheet1->setCellValue("A{$row}", $row - 1);
            $sheet1->setCellValue("B{$row}", $products[($row - 2) % count($products)] . ' ' . ($row - 1));
            $sheet1->setCellValue("C{$row}", $categories[($row - 2) % count($categories)]);
            $sheet1->setCellValue("D{$row}", round(10 + ($row * 1.37), 2));
            $sheet1->setCellValue("E{$row}", ($row % 10) + 1);
            // Subtotal formula: Price * Quantity
            $sheet1->setCellValue("F{$row}", "=D{$row}*E{$row}");
            // Tax formula: Subtotal * 0.1
            $sheet1->setCellValue("G{$row}", "=F{$row}*0.1");
            // Total formula: Subtotal + Tax
            $sheet1->setCellValue("H{$row}", "=F{$row}+G{$row}");

            // Alternating row colors
            if ($row % 2 === 0) {
                $sheet1->getStyle("A{$row}:H{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFD9E2F3');
            }
        }

        // Summary row
        $sumRow = 102;
        $sheet1->setCellValue("A{$sumRow}", 'TOTALS');
        $sheet1->getStyle("A{$sumRow}")->getFont()->setBold(true);
        $sheet1->setCellValue("F{$sumRow}", "=SUM(F2:F101)");
        $sheet1->setCellValue("G{$sumRow}", "=SUM(G2:G101)");
        $sheet1->setCellValue("H{$sumRow}", "=SUM(H2:H101)");

        // Add borders to the data range
        $sheet1->getStyle("A1:H{$sumRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color('FF000000'));

        // Number formats
        $sheet1->getStyle("D2:D{$sumRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet1->getStyle("F2:H{$sumRow}")->getNumberFormat()->setFormatCode('#,##0.00');

        // Sheet 2: Summary with cross-sheet references
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Summary');
        $sheet2->setCellValue('A1', 'Metric');
        $sheet2->setCellValue('B1', 'Value');
        $sheet2->getStyle('A1:B1')->getFont()->setBold(true);

        $sheet2->setCellValue('A2', 'Total Revenue');
        $sheet2->setCellValue('B2', '=SalesData!H102');
        $sheet2->setCellValue('A3', 'Total Tax');
        $sheet2->setCellValue('B3', '=SalesData!G102');
        $sheet2->setCellValue('A4', 'Average Price');
        $sheet2->setCellValue('B4', '=AVERAGE(SalesData!D2:D101)');
        $sheet2->setCellValue('A5', 'Item Count');
        $sheet2->setCellValue('B5', '=SUM(SalesData!E2:E101)');
        $sheet2->setCellValue('A6', 'Row Count');
        $sheet2->setCellValue('B6', '=COUNTA(SalesData!A2:A101)');

        // Sheet 3: More data to add zip entries
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Lookup');
        foreach ($categories as $i => $cat) {
            $rowNum = $i + 1;
            $sheet3->setCellValue("A{$rowNum}", $cat);
            $sheet3->setCellValue("B{$rowNum}", ($i + 1) * 0.05);
            $sheet3->getStyle("A{$rowNum}")->getFont()->setItalic(true);
        }

        $writer = new XlsxWriter($spreadsheet);
        $writer->save($path);
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Benchmark XLSX loading with the zip read cache.
     *
     * Times two consecutive loads on the same reader instance to verify
     * that the cache is correctly cleared between loads and both produce
     * valid results. Also measures memory usage.
     */
    public function testBenchmarkXlsxLoadWithZipCache(): void
    {
        $reader = new XlsxReader();

        // -- First load --
        $memBefore1 = memory_get_usage(true);
        $start1 = hrtime(true);

        $spreadsheet1 = $reader->load($this->tempFile);

        $elapsed1 = (hrtime(true) - $start1) / 1e6;
        $memAfter1 = memory_get_usage(true);
        $memDelta1 = ($memAfter1 - $memBefore1) / 1024 / 1024;

        // Verify correctness
        self::assertSame('SalesData', $spreadsheet1->getSheet(0)->getTitle());
        self::assertSame('Summary', $spreadsheet1->getSheet(1)->getTitle());
        self::assertSame('Lookup', $spreadsheet1->getSheet(2)->getTitle());
        self::assertSame(3, $spreadsheet1->getSheetCount());

        // Verify data integrity
        $dataSheet = $spreadsheet1->getSheet(0);
        self::assertSame('ID', $dataSheet->getCell('A1')->getValue());
        self::assertSame('Product', $dataSheet->getCell('B1')->getValue());
        self::assertSame('=D2*E2', $dataSheet->getCell('F2')->getValue());
        self::assertSame('TOTALS', $dataSheet->getCell('A102')->getValue());

        $peakMem1 = memory_get_peak_usage(true) / 1024 / 1024;

        $spreadsheet1->disconnectWorksheets();

        // -- Second load (same reader, cache should be cleared) --
        $memBefore2 = memory_get_usage(true);
        $start2 = hrtime(true);

        $spreadsheet2 = $reader->load($this->tempFile);

        $elapsed2 = (hrtime(true) - $start2) / 1e6;
        $memAfter2 = memory_get_usage(true);
        $memDelta2 = ($memAfter2 - $memBefore2) / 1024 / 1024;

        // Verify second load produces identical results
        self::assertSame(3, $spreadsheet2->getSheetCount());
        self::assertSame('SalesData', $spreadsheet2->getSheet(0)->getTitle());
        self::assertSame('ID', $spreadsheet2->getSheet(0)->getCell('A1')->getValue());
        self::assertSame('=D2*E2', $spreadsheet2->getSheet(0)->getCell('F2')->getValue());
        self::assertSame('TOTALS', $spreadsheet2->getSheet(0)->getCell('A102')->getValue());

        $peakMem2 = memory_get_peak_usage(true) / 1024 / 1024;

        $spreadsheet2->disconnectWorksheets();

        // -- Output benchmark results --
        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== XLSX Zip Read Cache Benchmark ===\n");
        fwrite(STDERR, sprintf("Test file: 3 sheets, 100 data rows, formulas + styles\n"));
        fwrite(STDERR, "\n");
        fwrite(STDERR, sprintf("First load:  %8.2f ms | Memory delta: %+.2f MB | Peak: %.2f MB\n", $elapsed1, $memDelta1, $peakMem1));
        fwrite(STDERR, sprintf("Second load: %8.2f ms | Memory delta: %+.2f MB | Peak: %.2f MB\n", $elapsed2, $memDelta2, $peakMem2));
        fwrite(STDERR, "\n");

        $diff = $elapsed1 - $elapsed2;
        $pct = $elapsed1 > 0 ? ($diff / $elapsed1) * 100 : 0;
        fwrite(STDERR, sprintf("Difference:  %+.2f ms (%.1f%%)\n", $diff, $pct));
        fwrite(STDERR, "=====================================\n");
        fwrite(STDERR, "\n");
    }

    /**
     * Benchmark listWorksheetNames and listWorksheetInfo which also
     * exercise the zip cache via getFromZipArchive calls.
     */
    public function testBenchmarkListOperationsWithZipCache(): void
    {
        $reader = new XlsxReader();
        $iterations = 5;

        // Benchmark listWorksheetNames
        $nameTimings = [];
        for ($i = 0; $i < $iterations; ++$i) {
            $start = hrtime(true);
            $names = $reader->listWorksheetNames($this->tempFile);
            $nameTimings[] = (hrtime(true) - $start) / 1e6;
        }

        self::assertSame(['SalesData', 'Summary', 'Lookup'], $names);

        // Benchmark listWorksheetInfo
        $infoTimings = [];
        for ($i = 0; $i < $iterations; ++$i) {
            $start = hrtime(true);
            $info = $reader->listWorksheetInfo($this->tempFile);
            $infoTimings[] = (hrtime(true) - $start) / 1e6;
        }

        self::assertCount(3, $info);
        self::assertSame('SalesData', $info[0]['worksheetName']);

        // Output results
        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== Zip Cache: List Operations Benchmark ===\n");
        fwrite(STDERR, sprintf("Iterations: %d\n", $iterations));
        fwrite(STDERR, "\n");

        $avgNames = array_sum($nameTimings) / count($nameTimings);
        $minNames = min($nameTimings);
        $maxNames = max($nameTimings);
        fwrite(STDERR, sprintf("listWorksheetNames:  avg %.2f ms | min %.2f ms | max %.2f ms\n", $avgNames, $minNames, $maxNames));

        $avgInfo = array_sum($infoTimings) / count($infoTimings);
        $minInfo = min($infoTimings);
        $maxInfo = max($infoTimings);
        fwrite(STDERR, sprintf("listWorksheetInfo:   avg %.2f ms | min %.2f ms | max %.2f ms\n", $avgInfo, $minInfo, $maxInfo));

        fwrite(STDERR, "=============================================\n");
        fwrite(STDERR, "\n");
    }

    /**
     * Benchmark loading with a fresh reader each time vs reusing a reader,
     * to measure the overhead of cache creation vs cache clearing.
     */
    public function testBenchmarkFreshReaderVsReusedReader(): void
    {
        $iterations = 3;

        // Fresh reader each time
        $freshTimings = [];
        $freshMemDeltas = [];
        for ($i = 0; $i < $iterations; ++$i) {
            $reader = new XlsxReader();
            $memBefore = memory_get_usage(true);
            $start = hrtime(true);

            $spreadsheet = $reader->load($this->tempFile);

            $freshTimings[] = (hrtime(true) - $start) / 1e6;
            $freshMemDeltas[] = (memory_get_usage(true) - $memBefore) / 1024 / 1024;
            $spreadsheet->disconnectWorksheets();
        }

        // Reused reader (cache cleared automatically on each load)
        $reusedReader = new XlsxReader();
        $reusedTimings = [];
        $reusedMemDeltas = [];
        for ($i = 0; $i < $iterations; ++$i) {
            $memBefore = memory_get_usage(true);
            $start = hrtime(true);

            $spreadsheet = $reusedReader->load($this->tempFile);

            $reusedTimings[] = (hrtime(true) - $start) / 1e6;
            $reusedMemDeltas[] = (memory_get_usage(true) - $memBefore) / 1024 / 1024;

            // Verify each load is correct
            self::assertSame(3, $spreadsheet->getSheetCount());
            self::assertSame('SalesData', $spreadsheet->getSheet(0)->getTitle());

            $spreadsheet->disconnectWorksheets();
        }

        // Output results
        fwrite(STDERR, "\n");
        fwrite(STDERR, "=== Zip Cache: Fresh vs Reused Reader ===\n");
        fwrite(STDERR, sprintf("Iterations: %d\n", $iterations));
        fwrite(STDERR, "\n");

        $avgFresh = array_sum($freshTimings) / count($freshTimings);
        $avgFreshMem = array_sum($freshMemDeltas) / count($freshMemDeltas);
        fwrite(STDERR, sprintf("Fresh reader:  avg %.2f ms | avg mem delta: %+.2f MB\n", $avgFresh, $avgFreshMem));

        for ($i = 0; $i < $iterations; ++$i) {
            fwrite(STDERR, sprintf("  Run %d: %.2f ms | mem: %+.2f MB\n", $i + 1, $freshTimings[$i], $freshMemDeltas[$i]));
        }

        fwrite(STDERR, "\n");

        $avgReused = array_sum($reusedTimings) / count($reusedTimings);
        $avgReusedMem = array_sum($reusedMemDeltas) / count($reusedMemDeltas);
        fwrite(STDERR, sprintf("Reused reader: avg %.2f ms | avg mem delta: %+.2f MB\n", $avgReused, $avgReusedMem));

        for ($i = 0; $i < $iterations; ++$i) {
            fwrite(STDERR, sprintf("  Run %d: %.2f ms | mem: %+.2f MB\n", $i + 1, $reusedTimings[$i], $reusedMemDeltas[$i]));
        }

        fwrite(STDERR, "=========================================\n");
        fwrite(STDERR, "\n");
    }
}
