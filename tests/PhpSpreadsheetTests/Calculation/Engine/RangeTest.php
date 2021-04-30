<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadSheet;

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
     * @param mixed $expectedResult
     */
    public function testRangeEvaluation(string $formula, $expectedResult): void
    {
        $workSheet = $this->spreadSheet->getActiveSheet();
        $workSheet->setCellValue('E1', $formula);

        $actualRresult = $workSheet->getCell('E1')->getCalculatedValue();
        self::assertSame($expectedResult, $actualRresult);
    }

    public function providerRangeEvaluation(): array
    {
        return[
            ['=SUM(A1:B3,A1:C2)', 48],
            ['=COUNT(A1:B3,A1:C2)', 12],
            ['=SUM(A1:B3 A1:C2)', 12],
            ['=COUNT(A1:B3 A1:C2)', 4],
            ['=SUM(A1:A3,C1:C3)', 30],
            ['=COUNT(A1:A3,C1:C3)', 6],
            ['=SUM(A1:A3 C1:C3)', Functions::null()],
            ['=COUNT(A1:A3 C1:C3)', 0],
            ['=SUM(A1:B2,B2:C3)', 40],
            ['=COUNT(A1:B2,B2:C3)', 8],
            ['=SUM(A1:B2 B2:C3)', 5],
            ['=COUNT(A1:B2 B2:C3)', 1],
            ['=SUM(A1:C1,A3:C3,B1:C3)', 63],
            ['=COUNT(A1:C1,A3:C3,B1:C3)', 12],
            ['=SUM(A1:C1,A3:C3 B1:C3)', 23],
            ['=COUNT(A1:C1,A3:C3 B1:C3)', 5],
            ['=SUM(Worksheet!A1:B3,Worksheet!A1:C2)', 48],
            ['=SUM(Worksheet!A1:Worksheet!B3,Worksheet!A1:Worksheet!C2)', 48],
        ];
    }

    public function test3dRangeEvaluation(): void
    {
        $workSheet = $this->spreadSheet->getActiveSheet();
        $workSheet->setCellValue('E1', '=SUM(Worksheet!A1:Worksheet2!B3)');

        $this->expectExceptionMessage('3D Range references are not yet supported');
        $workSheet->getCell('E1')->getCalculatedValue();
    }

    /**
     * @dataProvider providerNamedRangeEvaluation
     */
    public function testNamedRangeEvaluation(string $group1, string $group2, string $formula, int $expectedResult): void
    {
        $workSheet = $this->spreadSheet->getActiveSheet();
        $this->spreadSheet->addNamedRange(new NamedRange('GROUP1', $workSheet, $group1));
        $this->spreadSheet->addNamedRange(new NamedRange('GROUP2', $workSheet, $group2));

        $workSheet->setCellValue('E1', $formula);

        $sumRresult = $workSheet->getCell('E1')->getCalculatedValue();
        self::assertSame($expectedResult, $sumRresult);
    }

    public function providerNamedRangeEvaluation(): array
    {
        return[
            ['$A$1:$B$3', '$A$1:$C$2', '=SUM(GROUP1,GROUP2)', 48],
            ['$A$1:$B$3', '$A$1:$C$2', '=COUNT(GROUP1,GROUP2)', 12],
            ['$A$1:$B$3', '$A$1:$C$2', '=SUM(GROUP1 GROUP2)', 12],
            ['$A$1:$B$3', '$A$1:$C$2', '=COUNT(GROUP1 GROUP2)', 4],
            ['$A$1:$B$2', '$B$2:$C$3', '=SUM(GROUP1,GROUP2)', 40],
            ['$A$1:$B$2', '$B$2:$C$3', '=COUNT(GROUP1,GROUP2)', 8],
            ['$A$1:$B$2', '$B$2:$C$3', '=SUM(GROUP1 GROUP2)', 5],
            ['$A$1:$B$2', '$B$2:$C$3', '=COUNT(GROUP1 GROUP2)', 1],
            ['Worksheet!$A$1:$B$2', 'Worksheet!$B$2:$C$3', '=SUM(GROUP1,GROUP2)', 40],
            ['Worksheet!$A$1:Worksheet!$B$2', 'Worksheet!$B$2:Worksheet!$C$3', '=SUM(GROUP1,GROUP2)', 40],
        ];
    }

    /**
     * @dataProvider providerUTF8NamedRangeEvaluation
     *
     * @param string[] $names
     * @param string[] $ranges
     */
    public function testUTF8NamedRangeEvaluation(array $names, array $ranges, string $formula, int $expectedResult): void
    {
        $workSheet = $this->spreadSheet->getActiveSheet();
        foreach ($names as $index => $name) {
            $range = $ranges[$index];
            $this->spreadSheet->addNamedRange(new NamedRange($name, $workSheet, $range));
        }
        $workSheet->setCellValue('E1', $formula);

        $sumRresult = $workSheet->getCell('E1')->getCalculatedValue();
        self::assertSame($expectedResult, $sumRresult);
    }

    public function providerUTF8NamedRangeEvaluation(): array
    {
        return[
            [['Γειά', 'σου', 'Κόσμε'], ['$A$1', '$B$1:$B$2', '$C$1:$C$3'], '=SUM(Γειά,σου,Κόσμε)', 26],
            [['Γειά', 'σου', 'Κόσμε'], ['$A$1', '$B$1:$B$2', '$C$1:$C$3'], '=COUNT(Γειά,σου,Κόσμε)', 6],
            [['Здравствуй', 'мир'], ['$A$1:$A$3', '$C$1:$C$3'], '=SUM(Здравствуй,мир)', 30],
        ];
    }

    /**
     * @dataProvider providerCompositeNamedRangeEvaluation
     */
    public function testCompositeNamedRangeEvaluation(string $composite, int $expectedSum, int $expectedCount): void
    {
        self::markTestSkipped('must be revisited.');

        $workSheet = $this->spreadSheet->getActiveSheet();
        $this->spreadSheet->addNamedRange(new NamedRange('COMPOSITE', $workSheet, $composite));

        $workSheet->setCellValue('E1', '=SUM(COMPOSITE)');
        $workSheet->setCellValue('E2', '=COUNT(COMPOSITE)');

        $actualSum = $workSheet->getCell('E1')->getCalculatedValue();
        self::assertSame($expectedSum, $actualSum);
        $actualCount = $workSheet->getCell('E2')->getCalculatedValue();
        self::assertSame($expectedCount, $actualCount);
    }

    public function providerCompositeNamedRangeEvaluation(): array
    {
        return[
            //  Calculation engine doesn't yet handle union ranges with overlap
            'Union with overlap' => [
                'A1:C1,A3:C3,B1:C3',
                63,
                12,
            ],
            'Union and Intersection' => [
                'A1:C1,A3:C3 B1:C3',
                23,
                5,
            ],
        ];
    }
}
