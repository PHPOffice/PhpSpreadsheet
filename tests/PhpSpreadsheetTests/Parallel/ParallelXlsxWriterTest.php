<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Parallel;

use PhpOffice\PhpSpreadsheet\Parallel\Backend\PcntlBackend;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PHPUnit\Framework\TestCase;
use ZipArchive;

class ParallelXlsxWriterTest extends TestCase
{
    public function testParallelWriteProducesValidXlsx(): void
    {
        if (!PcntlBackend::isAvailable()) {
            self::markTestSkipped('pcntl extension not available');
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
            self::markTestSkipped('pcntl extension not available');
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
