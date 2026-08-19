<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class RowColumnReferenceTest extends TestCase
{
    protected Spreadsheet $spreadSheet;

    protected function setUp(): void
    {
        $this->spreadSheet = new Spreadsheet();

        $dataSheet = new Worksheet($this->spreadSheet, 'data sheet');
        $this->spreadSheet->addSheet($dataSheet, 0);
        $dataSheet->setCellValue('B1', 1.1);
        $dataSheet->setCellValue('B2', 2.2);
        $dataSheet->setCellValue('B3', 4.4);
        $dataSheet->setCellValue('C3', 8.8);
        $dataSheet->setCellValue('D3', 16.16);

        $calcSheet = new Worksheet($this->spreadSheet, 'summary sheet');
        $this->spreadSheet->addSheet($calcSheet, 1);
        $calcSheet->setCellValue('B1', 2.2);
        $calcSheet->setCellValue('B2', 4.4);
        $calcSheet->setCellValue('B3', 8.8);
        $calcSheet->setCellValue('C3', 16.16);
        $calcSheet->setCellValue('D3', 32.32);

        $this->spreadSheet->setActiveSheetIndexByName('summary sheet');
    }

    /**
     * VLOOKUP with a whole-column range where the end column contains no data.
     * getHighestDataRow() on the empty end column returned 1, producing an
     * inverted range (e.g. A4:F1) that caused a #N/A result.
     *
     * @see https://github.com/PHPOffice/PhpSpreadsheet/issues/XXXX
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerVlookupWholeColumnRange')]
    public function testVlookupWholeColumnRange(string $formula, string $expectedResult): void
    {
        $spreadsheet = new Spreadsheet();

        // Lookup sheet: col A = keys, col C = values; cols B, D, E, F are empty
        $lookupSheet = new Worksheet($spreadsheet, 'Sheet2');
        $spreadsheet->addSheet($lookupSheet, 0);
        $lookupSheet->fromArray([
            [1234, null, 'row1'],
            [2345, null, 'row2'],
            [3456, null, 'row3'],
            [4567, null, 'row4'],
        ], null, 'A1');

        // Formula sheet: C1 holds the lookup value
        $formulaSheet = new Worksheet($spreadsheet, 'Sheet1');
        $spreadsheet->addSheet($formulaSheet, 1);
        $formulaSheet->setCellValue('C1', 3456);
        $formulaSheet->setCellValue('A1', $formula);
        $spreadsheet->setActiveSheetIndexByName('Sheet1');

        $result = $formulaSheet->getCell('A1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
        $spreadsheet->disconnectWorksheets();
    }

    public static function providerVlookupWholeColumnRange(): array
    {
        return [
            'VLOOKUP with absolute whole-column range across sheets' => [
                '=VLOOKUP($C1,Sheet2!$A:$F,3,FALSE)',
                'row3',
            ],
            'VLOOKUP with relative whole-column range across sheets' => [
                '=VLOOKUP($C1,Sheet2!A:F,3,FALSE)',
                'row3',
            ],
            'VLOOKUP with mixed whole-column range across sheets' => [
                '=VLOOKUP($C1,Sheet2!A:$F,3,FALSE)',
                'row3',
            ],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerCurrentWorksheetFormulae')]
    public function testCurrentWorksheet(string $formula, float $expectedResult): void
    {
        $worksheet = $this->spreadSheet->getActiveSheet();

        $worksheet->setCellValue('A1', $formula);

        $result = $worksheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-12);
    }

    public static function providerCurrentWorksheetFormulae(): array
    {
        return [
            'relative range in active worksheet' => ['=SUM(B1:B3)', 15.4],
            'range with absolute columns in active worksheet' => ['=SUM($B1:$B3)', 15.4],
            'range with absolute rows in active worksheet' => ['=SUM(B$1:B$3)', 15.4],
            'range with absolute columns and rows in active worksheet' => ['=SUM($B$1:$B$3)', 15.4],
            'another relative range in active worksheet' => ['=SUM(B3:D3)', 57.28],
            'relative column range in active worksheet' => ['=SUM(B:B)', 15.4],
            'absolute column range in active worksheet' => ['=SUM($B:$B)', 15.4],
            'relative row range in active worksheet' => ['=SUM(3:3)', 57.28],
            'absolute row range in active worksheet' => ['=SUM($3:$3)', 57.28],
            'relative range in specified active worksheet' => ['=SUM(\'summary sheet\'!B1:B3)', 15.4],
            'range with absolute columns in specified active worksheet' => ['=SUM(\'summary sheet\'!$B1:$B3)', 15.4],
            'range with absolute rows in specified active worksheet' => ['=SUM(\'summary sheet\'!B$1:B$3)', 15.4],
            'range with absolute columns and rows in specified active worksheet' => ['=SUM(\'summary sheet\'!$B$1:$B$3)', 15.4],
            'another relative range in specified active worksheet' => ['=SUM(\'summary sheet\'!B3:D3)', 57.28],
            'relative column range in specified active worksheet' => ['=SUM(\'summary sheet\'!B:B)', 15.4],
            'absolute column range in specified active worksheet' => ['=SUM(\'summary sheet\'!$B:$B)', 15.4],
            'relative row range in specified active worksheet' => ['=SUM(\'summary sheet\'!3:3)', 57.28],
            'absolute row range in specified active worksheet' => ['=SUM(\'summary sheet\'!$3:$3)', 57.28],
            'relative range in specified other worksheet' => ['=SUM(\'data sheet\'!B1:B3)', 7.7],
            'range with absolute columns in specified other worksheet' => ['=SUM(\'data sheet\'!$B1:$B3)', 7.7],
            'range with absolute rows in specified other worksheet' => ['=SUM(\'data sheet\'!B$1:B$3)', 7.7],
            'range with absolute columns and rows in specified other worksheet' => ['=SUM(\'data sheet\'!$B$1:$B$3)', 7.7],
            'another relative range in specified other worksheet' => ['=SUM(\'data sheet\'!B3:D3)', 29.36],
            'relative column range in specified other worksheet' => ['=SUM(\'data sheet\'!B:B)', 7.7],
            'absolute column range in specified other worksheet' => ['=SUM(\'data sheet\'!$B:$B)', 7.7],
            'relative row range in specified other worksheet' => ['=SUM(\'data sheet\'!3:3)', 29.36],
            'absolute row range in specified other worksheet' => ['=SUM(\'data sheet\'!$3:$3)', 29.36],
        ];
    }
}
