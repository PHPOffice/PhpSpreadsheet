<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Exception as SpreadException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class MergedCellTest extends TestCase
{
    /**
     * @dataProvider providerWorksheetFormulaeColumns
     */
    public function testMergedCellColumns(string $formula, mixed $expectedResult): void
    {
        $spreadSheet = new Spreadsheet();

        $dataSheet = $spreadSheet->getActiveSheet();
        $dataSheet->setCellValue('A5', 3.3);
        $dataSheet->setCellValue('A3', 3.3);
        $dataSheet->setCellValue('A2', 2.2);
        $dataSheet->setCellValue('A1', 1.1);
        $dataSheet->setCellValue('B2', 2.2);
        $dataSheet->setCellValue('B1', 1.1);
        $dataSheet->setCellValue('C2', 4.4);
        $dataSheet->setCellValue('C1', 3.3);
        $dataSheet->mergeCells('A2:A4');
        $dataSheet->mergeCells('B:B');
        $worksheet = $spreadSheet->getActiveSheet();

        $worksheet->setCellValue('A7', $formula);

        $result = $worksheet->getCell('A7')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
        $spreadSheet->disconnectWorksheets();
    }

    public static function providerWorksheetFormulaeColumns(): array
    {
        return [
            ['=SUM(A1:A5)', 6.6],
            ['=COUNT(A1:A5)', 3],
            ['=COUNTA(A1:A5)', 3],
            ['=SUM(A3:A4)', 0],
            ['=A2+A3+A4', 2.2],
            ['=A2/A3', ExcelError::DIV0()],
            ['=SUM(B1:C2)', 8.8],
        ];
    }

    /**
     * @dataProvider providerWorksheetFormulaeRows
     */
    public function testMergedCellRows(string $formula, mixed $expectedResult): void
    {
        $spreadSheet = new Spreadsheet();

        $dataSheet = $spreadSheet->getActiveSheet();
        $dataSheet->setCellValue('A1', 1.1);
        $dataSheet->setCellValue('B1', 2.2);
        $dataSheet->setCellValue('C1', 3.3);
        $dataSheet->setCellValue('E1', 3.3);
        $dataSheet->setCellValue('A2', 1.1);
        $dataSheet->setCellValue('B2', 2.2);
        $dataSheet->setCellValue('A3', 3.3);
        $dataSheet->setCellValue('B3', 4.4);
        $dataSheet->mergeCells('B1:D1');
        $dataSheet->mergeCells('A2:B2');
        $worksheet = $spreadSheet->getActiveSheet();

        $worksheet->setCellValue('A7', $formula);

        $result = $worksheet->getCell('A7')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
        $spreadSheet->disconnectWorksheets();
    }

    public static function providerWorksheetFormulaeRows(): array
    {
        return [
            ['=SUM(A1:E1)', 6.6],
            ['=COUNT(A1:E1)', 3],
            ['=COUNTA(A1:E1)', 3],
            ['=SUM(C1:D1)', 0],
            ['=B1+C1+D1', 2.2],
            ['=B1/C1', ExcelError::DIV0()],
            ['=SUM(A2:B3)', 8.8],
        ];
    }

    private function setBadRange(Worksheet $sheet, string $range): void
    {
        try {
            $sheet->mergeCells($range);
            self::fail("Expected invalid merge range $range");
        } catch (SpreadException $e) {
            self::assertSame('Merge must be on a valid range of cells.', $e->getMessage());
        }
    }

    public function testMergedBadRange(): void
    {
        $spreadSheet = new Spreadsheet();

        $dataSheet = $spreadSheet->getActiveSheet();
        // TODO - Reinstate full validation and disallow single cell merging for version 2.0
//        $this->setBadRange($dataSheet, 'B1');
        $this->setBadRange($dataSheet, 'Invalid');
        $this->setBadRange($dataSheet, '1');
        $this->setBadRange($dataSheet, 'C');
        $this->setBadRange($dataSheet, 'B1:C');
        $this->setBadRange($dataSheet, 'B:C2');

        $spreadSheet->disconnectWorksheets();
    }
}
