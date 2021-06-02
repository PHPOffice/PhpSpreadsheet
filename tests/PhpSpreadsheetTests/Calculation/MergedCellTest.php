<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class MergedCellTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    protected $spreadSheet;

    protected function setUp(): void
    {
        $this->spreadSheet = new Spreadsheet();

        $dataSheet = $this->spreadSheet->getActiveSheet();
        $dataSheet->setCellValue('A1', 1.1);
        $dataSheet->setCellValue('A2', 2.2);
        $dataSheet->mergeCells('A2:A4');
        $dataSheet->setCellValue('A5', 3.3);
    }

    /**
     * @param mixed $expectedResult
     *
     * @dataProvider providerWorksheetFormulae
     */
    public function testMergedCellBehaviour(string $formula, $expectedResult): void
    {
        $worksheet = $this->spreadSheet->getActiveSheet();

        $worksheet->setCellValue('A7', $formula);

        $result = $worksheet->getCell('A7')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public function providerWorksheetFormulae(): array
    {
        return [
            ['=SUM(A1:A5)', 6.6],
            ['=COUNT(A1:A5)', 3],
            ['=COUNTA(A1:A5)', 3],
            ['=SUM(A3:A4)', 0],
            ['=A2+A3+A4', 2.2],
            ['=A2/A3', Functions::DIV0()],
        ];
    }
}
