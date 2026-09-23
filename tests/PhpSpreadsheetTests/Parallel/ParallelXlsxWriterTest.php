<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Parallel;

use PhpOffice\PhpSpreadsheet\Parallel\Backend\PcntlBackend;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column\Rule;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class ParallelXlsxWriterTest extends TestCase
{
    public function testParallelWriteProducesValidXlsx(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl backend not available (needs pcntl and fidry/cpu-core-counter)');
        }

        $spreadsheet = $this->createMultiSheetSpreadsheet();

        $tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet_parallel_test_');
        self::assertNotFalse($tempFile);

        try {
            $writer = new XlsxWriter($spreadsheet);
            $writer->setParallelEnabled(true);
            $writer->setMaxWorkers(2);
            $writer->save($tempFile);

            // Verify the file is a valid ZIP
            $zip = new ZipArchive();
            $opened = $zip->open($tempFile);
            self::assertTrue($opened === true);

            // Verify all sheet XMLs exist
            self::assertNotFalse($zip->locateName('xl/worksheets/sheet1.xml'));
            self::assertNotFalse($zip->locateName('xl/worksheets/sheet2.xml'));
            self::assertNotFalse($zip->locateName('xl/worksheets/sheet3.xml'));

            $zip->close();

            // Verify we can read it back and data is correct
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $loaded = $reader->load($tempFile);

            self::assertSame(3, $loaded->getSheetCount());
            self::assertSame('Sheet1', $loaded->getSheet(0)->getTitle());
            self::assertSame('Sheet2', $loaded->getSheet(1)->getTitle());
            self::assertSame('Sheet3', $loaded->getSheet(2)->getTitle());

            // Verify cell data
            self::assertSame('Hello', $loaded->getSheet(0)->getCell('A1')->getValue());
            self::assertSame(42, $loaded->getSheet(1)->getCell('A1')->getValue());
            self::assertSame('Third', $loaded->getSheet(2)->getCell('A1')->getValue());

            $loaded->disconnectWorksheets();
        } finally {
            @unlink($tempFile);
        }

        $spreadsheet->disconnectWorksheets();
    }

    public function testSequentialAndParallelProduceSameData(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl backend not available (needs pcntl and fidry/cpu-core-counter)');
        }

        $spreadsheet = $this->createMultiSheetSpreadsheet();

        $seqFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet_seq_');
        self::assertNotFalse($seqFile);

        $parFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet_par_');
        self::assertNotFalse($parFile);

        try {
            // Write sequentially (default)
            $writer1 = new XlsxWriter($spreadsheet);
            $writer1->save($seqFile);

            // Write in parallel
            $writer2 = new XlsxWriter($spreadsheet);
            $writer2->setParallelEnabled(true);
            $writer2->setMaxWorkers(2);
            $writer2->save($parFile);

            // Read both back and compare cell data
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $seqLoaded = $reader->load($seqFile);
            $parLoaded = $reader->load($parFile);

            self::assertSame($seqLoaded->getSheetCount(), $parLoaded->getSheetCount());

            for ($i = 0; $i < $seqLoaded->getSheetCount(); ++$i) {
                $seqSheet = $seqLoaded->getSheet($i);
                $parSheet = $parLoaded->getSheet($i);

                self::assertSame($seqSheet->getTitle(), $parSheet->getTitle());

                foreach ($seqSheet->getCoordinates() as $coord) {
                    self::assertSame(
                        $seqSheet->getCell($coord)->getValue(),
                        $parSheet->getCell($coord)->getValue(),
                        "Cell {$coord} on sheet {$seqSheet->getTitle()} differs"
                    );
                }
            }

            $seqLoaded->disconnectWorksheets();
            $parLoaded->disconnectWorksheets();
        } finally {
            @unlink($seqFile);
            @unlink($parFile);
        }

        $spreadsheet->disconnectWorksheets();
    }

    #[DataProvider('providerArrayReturnType')]
    public function testParallelWritesSameWorksheetXmlAsSequential(bool $returnArrayAsArray, string $expectedSheet2A1): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl backend not available (needs pcntl and fidry/cpu-core-counter)');
        }

        $sequential = $this->writeWorksheetXml(false, $returnArrayAsArray);
        $parallel = $this->writeWorksheetXml(true, $returnArrayAsArray);

        self::assertStringContainsString($expectedSheet2A1, $sequential['xl/worksheets/sheet2.xml']);
        self::assertSame($sequential, $parallel);
    }

    /** @return array<string, array{bool, string}> */
    public static function providerArrayReturnType(): array
    {
        return [
            // Sheet2!A1 reads the cell that the array formula in Sheet1!A1 spills into
            'array formulas' => [true, '<c r="A1"><f>Sheet1!A2</f><v>2</v></c>'],
            'value formulas' => [false, '<c r="A1"><f>Sheet1!A2</f><v>0</v></c>'],
        ];
    }

    public function testParallelSaveKeepsWriterChangesInModel(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl backend not available (needs pcntl and fidry/cpu-core-counter)');
        }

        $spreadsheet = $this->createSpreadsheetChangedByWriter(true);
        $tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet_parallel_model_');
        self::assertNotFalse($tempFile);

        try {
            $writer = new XlsxWriter($spreadsheet);
            $writer->setParallelEnabled(true);
            $writer->setMaxWorkers(2);
            $writer->save($tempFile);
        } finally {
            @unlink($tempFile);
        }

        $sheet1 = $spreadsheet->getSheetByNameOrThrow('Sheet1');
        $sheet2 = $spreadsheet->getSheetByNameOrThrow('Sheet2');
        self::assertSame(3, $sheet1->getCell('A3')->getValue(), 'array formula spill');
        self::assertGreaterThan(0.0, $sheet2->getColumnDimension('E')->getWidth(), 'auto-size width');
        self::assertTrue($sheet2->getAutoFilter()->getEvaluated(), 'autofilter evaluated');
        self::assertFalse($sheet2->getRowDimension(2)->getVisible(), 'filtered row hidden');
        self::assertTrue($sheet1->getTableByName('Numbers')?->getAutoFilter()->getEvaluated(), 'table filter evaluated');
        self::assertFalse($sheet1->getRowDimension(2)->getVisible(), 'table row hidden');

        $spreadsheet->disconnectWorksheets();
    }

    public function testParallelSettersAndGetters(): void
    {
        $spreadsheet = new Spreadsheet();
        $writer = new XlsxWriter($spreadsheet);

        // Defaults
        self::assertFalse($writer->isParallelEnabled());
        self::assertNull($writer->getMaxWorkers());

        // Set values
        $result = $writer->setParallelEnabled(true);
        self::assertSame($writer, $result);
        self::assertTrue($writer->isParallelEnabled());

        $result = $writer->setMaxWorkers(4);
        self::assertSame($writer, $result);
        self::assertSame(4, $writer->getMaxWorkers());

        self::assertSame(0, $writer->getParallelTimeout());
        $result = $writer->setParallelTimeout(120);
        self::assertSame($writer, $result);
        self::assertSame(120, $writer->getParallelTimeout());
        $writer->setParallelTimeout(0);

        // Reset
        $writer->setParallelEnabled(false);
        self::assertFalse($writer->isParallelEnabled());

        $writer->setMaxWorkers(null);
        self::assertNull($writer->getMaxWorkers());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSingleSheetFallsBackToSequentialEvenWhenParallelEnabled(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Only sheet');

        $tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet_parallel_single_');
        self::assertNotFalse($tempFile);

        try {
            $writer = new XlsxWriter($spreadsheet);
            $writer->setParallelEnabled(true);
            $writer->save($tempFile);

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $loaded = $reader->load($tempFile);
            self::assertSame(1, $loaded->getSheetCount());
            self::assertSame('Only sheet', $loaded->getSheet(0)->getCell('A1')->getValue());
            $loaded->disconnectWorksheets();
        } finally {
            @unlink($tempFile);
        }

        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Save the spreadsheet from createSpreadsheetChangedByWriter().
     *
     * @return array<string, string> worksheet XML keyed by archive path
     */
    private function writeWorksheetXml(bool $parallel, bool $returnArrayAsArray): array
    {
        $spreadsheet = $this->createSpreadsheetChangedByWriter($returnArrayAsArray);
        $tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet_parallel_xml_');
        self::assertNotFalse($tempFile);

        $xml = [];

        try {
            $writer = new XlsxWriter($spreadsheet);
            $writer->setParallelEnabled($parallel);
            $writer->setMaxWorkers(2);
            // Same seed for both saves, so that RAND() gives the same values
            mt_srand(4834);
            $writer->save($tempFile);

            $zip = new ZipArchive();
            self::assertTrue($zip->open($tempFile) === true);
            for ($i = 1; $i <= $spreadsheet->getSheetCount(); ++$i) {
                $name = "xl/worksheets/sheet{$i}.xml";
                $content = $zip->getFromName($name);
                self::assertIsString($content);
                $xml[$name] = $content;
            }
            $zip->close();
        } finally {
            mt_srand();
            @unlink($tempFile);
            $spreadsheet->disconnectWorksheets();
        }

        return $xml;
    }

    /**
     * Writing a worksheet changes the model: it spills array formulas,
     * applies autofilters and sets auto-size widths. Sheet2!A1 reads a cell
     * that Sheet1!A1 spills into (array formulas only).
     */
    private function createSpreadsheetChangedByWriter(bool $returnArrayAsArray): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        if ($returnArrayAsArray) {
            $spreadsheet->returnArrayAsArray();
        }

        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sheet1');
        $sheet1->setCellValue('A1', '=SEQUENCE(3)');
        // A CSE array formula, as the Xlsx reader loads it
        $sheet1->setCellValue('C1', '=SUM(F2:F3*F3:F4)');
        $sheet1->getCell('C1')->setFormulaAttributes(['t' => 'array', 'ref' => 'C1']);
        $sheet1->fromArray([['Numbers'], [1], [2], [3]], null, 'F1');
        $table = new Table('F1:F4', 'Numbers');
        $sheet1->addTable($table);
        $tableFilter = $table->getAutoFilter()->getColumn('F');
        $tableFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $tableFilter->createRule()->setRule(Rule::AUTOFILTER_COLUMN_RULE_EQUAL, 3);

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet2');
        $sheet2->setCellValue('A1', '=Sheet1!A2');
        $sheet2->fromArray([['Filtered'], [1], [2], [3]], null, 'D1');
        $autoFilter = $sheet2->getAutoFilter();
        $autoFilter->setRange('D1:D4');
        $columnFilter = $autoFilter->getColumn('D');
        $columnFilter->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);
        $columnFilter->createRule()->setRule(Rule::AUTOFILTER_COLUMN_RULE_EQUAL, 2);
        $sheet2->setCellValue('E1', 'A long text that sets the auto-size width');
        $sheet2->getColumnDimension('E')->setAutoSize(true);

        if ($returnArrayAsArray) {
            // The parent calculates array formulas before the fork, in the
            // same order as the sequential writer, so volatile values agree
            // across sheets. Sheet2!C1 reads Sheet1!B1 after Sheet2 made its
            // own RAND() calls. B2 comes before B1, because the writer
            // calculates array formulas in insertion order.
            $sheet1->setCellValue('B1', '=RAND()');
            $sheet2->setCellValue('B2', '=RAND()');
            $sheet2->setCellValue('B1', '=RAND()');
            $sheet2->setCellValue('C1', '=Sheet1!B1');
        }

        return $spreadsheet;
    }

    private function createMultiSheetSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        // Sheet 1
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Sheet1');
        $sheet1->setCellValue('A1', 'Hello');
        $sheet1->setCellValue('B1', 'World');
        for ($row = 2; $row <= 100; ++$row) {
            $sheet1->setCellValue("A{$row}", "Row {$row}");
            $sheet1->setCellValue("B{$row}", $row * 10);
        }

        // Sheet 2
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sheet2');
        $sheet2->setCellValue('A1', 42);
        $sheet2->setCellValue('B1', 3.14);
        for ($row = 2; $row <= 100; ++$row) {
            $sheet2->setCellValue("A{$row}", $row);
            $sheet2->setCellValue("B{$row}", $row * 0.5);
        }

        // Sheet 3
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Sheet3');
        $sheet3->setCellValue('A1', 'Third');
        $sheet3->setCellValue('B1', true);
        for ($row = 2; $row <= 100; ++$row) {
            $sheet3->setCellValue("A{$row}", "Data {$row}");
        }

        return $spreadsheet;
    }
}
