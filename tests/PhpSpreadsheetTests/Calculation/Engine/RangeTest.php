<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    protected $spreadSheet;

    protected function setUp(): void
    {
        $this->spreadSheet = new Spreadsheet();
        $this->spreadSheet->getActiveSheet()
            ->setCellValue('A1', 1)
            ->setCellValue('B1', 2)
            ->setCellValue('C1', 3)
            ->setCellValue('A2', 4)
            ->setCellValue('B2', 5)
            ->setCellValue('C2', 6)
            ->setCellValue('A3', 7)
            ->setCellValue('B3', 8)
            ->setCellValue('C3', 9);
    }

    /**
     * @dataProvider providerRangeEvaluation
     *
     * @param string $formula
     * @param int $expectedResult
     */
    public function testRangeEvaluation($formula, $expectedResult): void
    {
        $workSheet = $this->spreadSheet->getActiveSheet();
        $workSheet->setCellValue('E1', $formula);

        $actualRresult = $workSheet->getCell('E1')->getCalculatedValue();
        self::assertSame($expectedResult, $actualRresult);
    }

    public function providerRangeEvaluation()
    {
        return[
            ['=SUM(A1:B3,A1:C2)', 48],
            ['=SUM(A1:B3 A1:C2)', 12],
            ['=SUM(A1:A3,C1:C3)', 30],
            ['=SUM(A1:A3 C1:C3)', Functions::null()],
            ['=SUM(A1:B2,B2:C3)', 40],
            ['=SUM(A1:B2 B2:C3)', 5],
        ];
    }

    /**
     * @dataProvider providerNamedRangeEvaluation
     *
     * @param string $group1
     * @param string $group2
     * @param string $formula
     * @param int $expectedResult
     */
    public function testNamedRangeEvaluation($group1, $group2, $formula, $expectedResult): void
    {
        $workSheet = $this->spreadSheet->getActiveSheet();
        $this->spreadSheet->addNamedRange(new NamedRange('GROUP1', $workSheet, $group1));
        $this->spreadSheet->addNamedRange(new NamedRange('GROUP2', $workSheet, $group2));

        $workSheet->setCellValue('E1', $formula);

        $actualRresult = $workSheet->getCell('E1')->getCalculatedValue();
        self::assertSame($expectedResult, $actualRresult);
    }

    public function providerNamedRangeEvaluation()
    {
        return[
            [
                'A1:B3',
                'A1:C2',
                '=SUM(GROUP1,GROUP2)',
                48,
            ],
            [
                'A1:B3',
                'A1:C2',
                '=SUM(GROUP1 GROUP2)',
                12,
            ],
            [
                'A1:B2',
                'B2:C3',
                '=SUM(GROUP1,GROUP2)',
                40,
            ],
            [
                'A1:B2',
                'B2:C3',
                '=SUM(GROUP1 GROUP2)',
                5,
            ],
        ];
    }
}
