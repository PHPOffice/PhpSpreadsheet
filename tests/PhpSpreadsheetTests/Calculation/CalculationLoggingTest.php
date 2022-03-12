<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CalculationLoggingTest extends TestCase
{
    public function testFormulaWithLogging(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(
            [
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 9],
            ]
        );

        $debugLog = Calculation::getInstance($spreadsheet)->getDebugLog();
        $debugLog->setWriteDebugLog(true);

        $cell = $sheet->getCell('E5');
        $cell->setValue('=ROUND(SQRT(SUM(A1:C3)), 1)');
        self::assertEquals(6.7, $cell->getCalculatedValue());

        $log = $debugLog->getLog();
        self::assertIsArray($log);
        $entries = count($log);
        self::assertGreaterThan(0, $entries);

        $finalEntry = array_pop($log);
        self::assertStringContainsString('Evaluation Result', $finalEntry);
    }

    public function testFormulaWithMultipleCellLogging(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(
            [
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 9],
            ]
        );

        $debugLog = Calculation::getInstance($spreadsheet)->getDebugLog();
        $debugLog->setWriteDebugLog(true);

        $cell = $sheet->getCell('E1');
        $cell->setValue('=SUM(A1:C3)');

        $cell = $sheet->getCell('E3');
        $cell->setValue('=SQRT(E1)');

        $cell = $sheet->getCell('E5');
        $cell->setValue('=ROUND(E3, 1)');
        self::assertEquals(6.7, $cell->getCalculatedValue());

        $log = $debugLog->getLog();

        self::assertIsArray($log);
        $entries = count($log);
        self::assertGreaterThan(0, $entries);

        $finalEntry = array_pop($log);
        self::assertStringContainsString('Evaluation Result', $finalEntry);

        $e1Log = array_filter($log, function ($entry) {
            return strpos($entry, 'E1') !== false;
        });
        $e1Entries = count($e1Log);
        self::assertGreaterThan(0, $e1Entries);

        $e1FinalEntry = array_pop($e1Log);
        self::assertStringContainsString('Evaluation Result', $e1FinalEntry);

        $e3Log = array_filter($log, function ($entry) {
            return strpos($entry, 'E1') !== false;
        });
        $e3Entries = count($e3Log);
        self::assertGreaterThan(0, $e3Entries);

        $e3FinalEntry = array_pop($e3Log);
        self::assertStringContainsString('Evaluation Result', $e3FinalEntry);
    }

    public function testFlushLog(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(
            [
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 9],
            ]
        );

        $debugLog = Calculation::getInstance($spreadsheet)->getDebugLog();
        $debugLog->setWriteDebugLog(true);

        $cell = $sheet->getCell('E5');
        $cell->setValue('=(1+-2)*3/4');
        self::assertEquals(-0.75, $cell->getCalculatedValue());

        $log = $debugLog->getLog();
        self::assertIsArray($log);
        $entries = count($log);
        self::assertGreaterThan(0, $entries);

        $debugLog->clearLog();

        $log = $debugLog->getLog();
        self::assertIsArray($log);
        self::assertEmpty($log);
    }
}
