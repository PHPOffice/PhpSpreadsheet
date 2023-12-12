<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
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

    public function testStructuredReferenceHiddenHeaders(): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/TableFormulae.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);
        /** @var Table $table */
        $table = $spreadsheet->getActiveSheet()->getTableByName('DeptSales');

        $cellAddress = 'G8';
        $spreadsheet->getActiveSheet()->getCell($cellAddress)->setValue('=DeptSales[[#Headers][Region]]');
        $result = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame('Region', $result);

        $spreadsheet->getCalculationEngine()?->flushInstance();
        $table->setShowHeaderRow(false);

        $result = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame(ExcelError::REF(), $result);
    }

    public function testStructuredReferenceInvalidColumn(): void
    {
        $inputFileType = 'Xlsx';
        $inputFileName = __DIR__ . '/../../data/Calculation/TableFormulae.xlsx';

        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($inputFileName);

        $cellAddress = 'E2';
        $spreadsheet->getActiveSheet()->getCell($cellAddress)->setValue('=[@Sales Amount]*[@[%age Commission]]');

        $result = $spreadsheet->getActiveSheet()->getCell($cellAddress)->getCalculatedValue();
        self::assertSame(ExcelError::REF(), $result);
    }

    public static function structuredReferenceProvider(): array
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
