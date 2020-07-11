<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class DefinedNamesCalculationTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setUp(): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/DefinedNames/RelativeNamedRanges.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $this->spreadsheet = $reader->load($inputFileName);
        Calculation::getInstance($this->spreadsheet)->clearCalculationCache();
    }

    /**
     * @dataProvider namedRangeCalculationTest1
     */
    public function testNamedRangeCalculations1($cellAddress, $expectedValue): void
    {
        $calculatedCellValue = $this->spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame($expectedValue, $calculatedCellValue, "Failed calculation for cell {$cellAddress}");
    }

    public function namedRangeCalculationTest1()
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

    /**
     * @dataProvider namedRangeCalculationTest2
     */
    public function testNamedRangeCalculationsWithAdjustedRateValue($cellAddress, $expectedValue): void
    {
        $this->spreadsheet->getActiveSheet()->getCell('B1')->setValue(12.5);

        $calculatedCellValue = $this->spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame($expectedValue, $calculatedCellValue, "Failed calculation for cell {$cellAddress}");
    }

    public function namedRangeCalculationTest2()
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
