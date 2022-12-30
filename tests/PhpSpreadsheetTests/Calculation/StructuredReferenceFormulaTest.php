<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPUnit\Framework\TestCase;

class StructuredReferenceFormulaTest extends TestCase
{
    /**
     * @dataProvider structuredReferenceProvider
     */
    public function testStructuredReferences(float $expectedValue, string $cellAddress): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/TableFormulae.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        $calculatedCellValue = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertEqualsWithDelta($expectedValue, $calculatedCellValue, 1.0e-14, "Failed calculation for cell {$cellAddress}");
    }

    public function structuredReferenceProvider(): array
    {
        return [
            [26.0, 'E2'],
            [99.0, 'E3'],
            [141.0, 'E4'],
            [49.2, 'E5'],
            [120.0, 'E6'],
            [135.0, 'E7'],
            [570.2, 'E8'],
            [3970.0, 'C8'],
        ];
    }
}
