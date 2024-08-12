<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    private string $incompleteMessage = 'Must be revisited';

    private ?Spreadsheet $spreadSheet = null;

    protected function getSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()
            ->fromArray(array_chunk(range(1, 240), 6), null, 'A1', true);

        return $spreadsheet;
    }

    protected function tearDown(): void
    {
        if ($this->spreadSheet !== null) {
            $this->spreadSheet->disconnectWorksheets();
            $this->spreadSheet = null;
        }
    }

    /**
     * @dataProvider providerRangeEvaluation
     */
    public function testRangeEvaluation(string $formula, int|string $expectedResult): void
    {
        $this->spreadSheet = $this->getSpreadsheet();
        $workSheet = $this->spreadSheet->getActiveSheet();
        $workSheet->setCellValue('H1', $formula);

        $actualRresult = $workSheet->getCell('H1')->getCalculatedValue();
        self::assertSame($expectedResult, $actualRresult);
    }

    public static function providerRangeEvaluation(): array
    {
        return [
            'Sum with Simple Range' => ['=SUM(A1:C3)', 72],
            'Count with Simple Range' => ['=COUNT(A1:C3)', 9],
            'Sum with UNION #1' => ['=SUM(A1:B3,A1:C2)', 75],
            'Count with UNION #1' => ['=COUNT(A1:B3,A1:C2)', 12],
            'Sum with INTERSECTION #1' => ['=SUM(A1:B3 A1:C2)', 18],
            'Count with INTERSECTION #1' => ['=COUNT(A1:B3 A1:C2)', 4],
            'Sum with UNION #2' => ['=SUM(A1:A3,C1:C3)', 48],
            'Count with UNION #2' => ['=COUNT(A1:A3,C1:C3)', 6],
            'Sum with INTERSECTION #2 - No Intersect' => ['=SUM(A1:A3 C1:C3)', ExcelError::null()],
            'Count with INTERSECTION #2 - No Intersect' => ['=COUNT(A1:A3 C1:C3)', 0],
            'Sum with UNION #3' => ['=SUM(A1:B2,B2:C3)', 64],
            'Count with UNION #3' => ['=COUNT(A1:B2,B2:C3)', 8],
            'Sum with INTERSECTION #3 - Single Cell' => ['=SUM(A1:B2 B2:C3)', 8],
            'Count with INTERSECTION #3 - Single Cell' => ['=COUNT(A1:B2 B2:C3)', 1],
            'Sum with Triple UNION' => ['=SUM(A1:C1,A3:C3,B1:C3)', 99],
            'Count with Triple UNION' => ['=COUNT(A1:C1,A3:C3,B1:C3)', 12],
            'Sum with UNION and INTERSECTION' => ['=SUM(A1:C1,A3:C3 B1:C3)', 35],
            'Count with UNION and INTERSECTION' => ['=COUNT(A1:C1,A3:C3 B1:C3)', 5],
            'Sum with UNION with Worksheet Reference' => ['=SUM(Worksheet!A1:B3,Worksheet!A1:C2)', 75],
            'Sum with UNION with full Worksheet Reference' => ['=SUM(Worksheet!A1:Worksheet!B3,Worksheet!A1:Worksheet!C2)', 75],
            'Sum with Chained UNION #1' => ['=SUM(A3:B1:C2)', 72],
            'Count with Chained UNION #1' => ['=COUNT(A3:B1:C2)', 9],
            'Sum with Chained UNION #2' => ['=SUM(A5:C10:C20:F1)', 7260],
            'Count with Chained UNION#2' => ['=COUNT(A5:C10:C20:F1)', 120],
        ];
    }

    public function test3dRangeParsing(): void
    {
        // This test shows that parsing throws exception.
        // Next test shows that formula is still treated as a formula
        //     despite the parse failure.
        $this->expectExceptionMessage('3D Range references are not yet supported');
        $calculation = new Calculation();
        $calculation->disableBranchPruning();
        $calculation->parseFormula('=SUM(Worksheet!A1:Worksheet2!B3');
    }

    public function test3dRangeEvaluation(): void
    {
        $this->spreadSheet = $this->getSpreadsheet();
        $workSheet = $this->spreadSheet->getActiveSheet();
        $workSheet->setCellValue('E1', '=SUM(Worksheet!A1:Worksheet2!B3)');

        $this->expectExceptionMessage('3D Range references are not yet supported');
        $workSheet->getCell('E1')->getCalculatedValue();
    }

    /**
     * @dataProvider providerNamedRangeEvaluation
     */
    public function testNamedRangeEvaluation(array $ranges, string $formula, int $expectedResult): void
    {
        $this->spreadSheet = $this->getSpreadsheet();
        $workSheet = $this->spreadSheet->getActiveSheet();
        foreach ($ranges as $id => $range) {
            $this->spreadSheet->addNamedRange(new NamedRange('GROUP' . ++$id, $workSheet, $range));
        }

        $workSheet->setCellValue('H1', $formula);

        $sumRresult = $workSheet->getCell('H1')->getCalculatedValue();
        self::assertSame($expectedResult, $sumRresult);
    }

    public static function providerNamedRangeEvaluation(): array
    {
        return [
            [['$A$1:$B$3', '$A$1:$C$2'], '=SUM(GROUP1,GROUP2)', 75],
            [['$A$1:$B$3', '$A$1:$C$2'], '=COUNT(GROUP1,GROUP2)', 12],
            [['$A$1:$B$3', '$A$1:$C$2'], '=SUM(GROUP1 GROUP2)', 18],
            [['$A$1:$B$3', '$A$1:$C$2'], '=COUNT(GROUP1 GROUP2)', 4],
            [['$A$1:$B$2', '$B$2:$C$3'], '=SUM(GROUP1,GROUP2)', 64],
            [['$A$1:$B$2', '$B$2:$C$3'], '=COUNT(GROUP1,GROUP2)', 8],
            [['$A$1:$B$2', '$B$2:$C$3'], '=SUM(GROUP1 GROUP2)', 8],
            [['$A$1:$B$2', '$B$2:$C$3'], '=COUNT(GROUP1 GROUP2)', 1],
            [['$A$5', '$C$10:$C$20', '$F$1'], '=SUM(GROUP1:GROUP2:GROUP3)', 7260],
            [['$A$5:$A$7', '$C$20', '$F$1'], '=SUM(GROUP1:GROUP2:GROUP3)', 7260],
            [['$A$5:$A$7', '$C$10:$C$20', '$F$1'], '=SUM(GROUP1:GROUP2:GROUP3)', 7260],
            [['Worksheet!$A$1:$B$2', 'Worksheet!$B$2:$C$3'], '=SUM(GROUP1,GROUP2)', 64],
            [['Worksheet!$A$1:Worksheet!$B$2', 'Worksheet!$B$2:Worksheet!$C$3'], '=SUM(GROUP1,GROUP2)', 64],
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
        $this->spreadSheet = $this->getSpreadsheet();
        $workSheet = $this->spreadSheet->getActiveSheet();
        foreach ($names as $index => $name) {
            $range = $ranges[$index];
            $this->spreadSheet->addNamedRange(new NamedRange($name, $workSheet, $range));
        }
        $workSheet->setCellValue('E1', $formula);

        $sumRresult = $workSheet->getCell('E1')->getCalculatedValue();
        self::assertSame($expectedResult, $sumRresult);
    }

    public static function providerUTF8NamedRangeEvaluation(): array
    {
        return [
            [['Γειά', 'σου', 'Κόσμε'], ['$A$1', '$B$1:$B$2', '$C$1:$C$3'], '=SUM(Γειά,σου,Κόσμε)', 38],
            [['Γειά', 'σου', 'Κόσμε'], ['$A$1', '$B$1:$B$2', '$C$1:$C$3'], '=COUNT(Γειά,σου,Κόσμε)', 6],
            [['Здравствуй', 'мир'], ['$A$1:$A$3', '$C$1:$C$3'], '=SUM(Здравствуй,мир)', 48],
        ];
    }

    /**
     * @dataProvider providerCompositeNamedRangeEvaluation
     */
    public function testCompositeNamedRangeEvaluation(string $composite, int $expectedSum, int $expectedCount): void
    {
        if ($this->incompleteMessage !== '') {
            self::markTestIncomplete($this->incompleteMessage);
        }
        $this->spreadSheet = $this->getSpreadsheet();

        $workSheet = $this->spreadSheet->getActiveSheet();
        $this->spreadSheet->addNamedRange(new NamedRange('COMPOSITE', $workSheet, $composite));

        $workSheet->setCellValue('E1', '=SUM(COMPOSITE)');
        $workSheet->setCellValue('E2', '=COUNT(COMPOSITE)');

        $actualSum = $workSheet->getCell('E1')->getCalculatedValue();
        self::assertSame($expectedSum, $actualSum);
        $actualCount = $workSheet->getCell('E2')->getCalculatedValue();
        self::assertSame($expectedCount, $actualCount);
    }

    public static function providerCompositeNamedRangeEvaluation(): array
    {
        return [
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
