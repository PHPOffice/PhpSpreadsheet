<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DefinedNamesCalculationTest extends TestCase
{
    #[DataProvider('namedRangeCalculationProvider1')]
    public function testNamedRangeCalculations1(string $cellAddress, float $expectedValue): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/DefinedNames/NamedRanges.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        $calculatedCellValue = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame($expectedValue, $calculatedCellValue, "Failed calculation for cell {$cellAddress}");
        $spreadsheet->disconnectWorksheets();
    }

    public function testNamedRangeCalculationsIfError(): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/DefinedNames/NamedRanges.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('E1')
            ->setValue('=IFERROR(CHARGE_RATE, 999)');
        $sheet->getCell('F1')
            ->setValue('=IFERROR(CHARGE_RATX, 999)');
        self::assertSame(7.5, $sheet->getCell('E1')->getCalculatedValue());
        self::assertSame(999, $sheet->getCell('F1')->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    #[DataProvider('namedRangeCalculationProvider2')]
    public function testNamedRangeCalculationsWithAdjustedRateValue(string $cellAddress, float $expectedValue): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/DefinedNames/NamedRanges.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        $spreadsheet->getActiveSheet()->getCell('B1')->setValue(12.5);

        $calculatedCellValue = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame($expectedValue, $calculatedCellValue, "Failed calculation for cell {$cellAddress}");
        $spreadsheet->disconnectWorksheets();
    }

    #[DataProvider('namedRangeCalculationProvider1')]
    public function testNamedFormulaCalculations1(string $cellAddress, float $expectedValue): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/DefinedNames/NamedFormulae.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        $calculatedCellValue = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame($expectedValue, $calculatedCellValue, "Failed calculation for cell {$cellAddress}");
        $spreadsheet->disconnectWorksheets();
    }

    #[DataProvider('namedRangeCalculationProvider2')]
    public function testNamedFormulaeCalculationsWithAdjustedRateValue(string $cellAddress, float $expectedValue): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/DefinedNames/NamedFormulae.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        $spreadsheet->getActiveSheet()->getCell('B1')->setValue(12.5);

        $calculatedCellValue = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame($expectedValue, $calculatedCellValue, "Failed calculation for cell {$cellAddress}");
        $spreadsheet->disconnectWorksheets();
    }

    public static function namedRangeCalculationProvider1(): array
    {
        return [
            ['C4', 56.25],
            ['C5', 54.375],
            ['C6', 48.75],
            ['C7', 52.5],
            ['C8', 41.25],
            ['B10', 33.75],
            ['C10', 253.125],
        ];
    }

    public static function namedRangeCalculationProvider2(): array
    {
        return [
            ['C4', 93.75],
            ['C5', 90.625],
            ['C6', 81.25],
            ['C7', 87.5],
            ['C8', 68.75],
            ['C10', 421.875],
        ];
    }
}
