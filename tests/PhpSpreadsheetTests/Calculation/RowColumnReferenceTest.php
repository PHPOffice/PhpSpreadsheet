<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class RowColumnReferenceTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    protected $spreadSheet;

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
     * @dataProvider providerCurrentWorksheetFormulae
     */
    public function testCurrentWorksheet(string $formula, float $expectedResult): void
    {
        $worksheet = $this->spreadSheet->getActiveSheet();

        $worksheet->setCellValue('A1', $formula);

        $result = $worksheet->getCell('A1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public function providerCurrentWorksheetFormulae(): array
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
