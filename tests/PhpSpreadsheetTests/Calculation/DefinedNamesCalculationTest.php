<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class DefinedNamesCalculationTest extends TestCase
{
    /**
     * @dataProvider namedRangeCalculationTest1
     */
    public function testNamedRangeCalculations1(string $cellAddress, float $expectedValue): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/DefinedNames/NamedRanges.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        $calculatedCellValue = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame($expectedValue, $calculatedCellValue, "Failed calculation for cell {$cellAddress}");
    }

    /**
     * @dataProvider namedRangeCalculationTest2
     */
    public function testNamedRangeCalculationsWithAdjustedRateValue(string $cellAddress, float $expectedValue): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/DefinedNames/NamedRanges.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        $spreadsheet->getActiveSheet()->getCell('B1')->setValue(12.5);

        $calculatedCellValue = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame($expectedValue, $calculatedCellValue, "Failed calculation for cell {$cellAddress}");
    }

    /**
     * @dataProvider namedRangeCalculationTest1
     */
    public function testNamedFormulaCalculations1(string $cellAddress, float $expectedValue): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/DefinedNames/NamedFormulae.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        $calculatedCellValue = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame($expectedValue, $calculatedCellValue, "Failed calculation for cell {$cellAddress}");
    }

    /**
     * @dataProvider namedRangeCalculationTest2
     */
    public function testNamedFormulaeCalculationsWithAdjustedRateValue(string $cellAddress, float $expectedValue): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/DefinedNames/NamedFormulae.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        $spreadsheet->getActiveSheet()->getCell('B1')->setValue(12.5);

        $calculatedCellValue = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame($expectedValue, $calculatedCellValue, "Failed calculation for cell {$cellAddress}");
    }

    public static function namedRangeCalculationTest1(): array
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

    public static function namedRangeCalculationTest2(): array
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
