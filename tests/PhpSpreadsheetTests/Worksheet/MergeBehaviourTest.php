<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheetTests\Functional\AbstractFunctional;

class MergeBehaviourTest extends AbstractFunctional
{
    private static array $testDataRaw = [
        [1.1, 2.2, '=ROUND(A1+B1, 1)'],
        [4.4, 5.5, '=ROUND(A2+B2, 1)'],
        ['=ROUND(A1+A2, 1)', '=ROUND(B1+B2, 1)', '=ROUND(A3+B3, 1)'],
    ];

    private array $testDataFormatted = [
        ['=DATE(1960, 12, 19)', '=DATE(2022, 09, 15)'],
    ];

    public function testMergeCellsDefaultBehaviour(): void
    {
        $expectedResult = [
            [1.1, null, null],
            [null, null, null],
            [null, null, null],
        ];

        $mergeRange = 'A1:C3';
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray(self::$testDataRaw, null, 'A1', true);
        $worksheet->mergeCells($mergeRange);

        $mergeResult = $worksheet->toArray(null, true, false, false);
        self::assertSame($expectedResult, $mergeResult);
        $spreadsheet->disconnectWorksheets();
    }

    public function testMergeCellsDefaultBehaviourFormatted(): void
    {
        $expectedResult = [
            ['1960-12-19', null],
        ];

        $mergeRange = 'A1:B1';
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray($this->testDataFormatted, null, 'A1', true);
        $worksheet->getStyle($mergeRange)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $worksheet->mergeCells($mergeRange);

        $mergeResult = $worksheet->toArray(null, true, true, false);
        self::assertSame($expectedResult, $mergeResult);
        $spreadsheet->disconnectWorksheets();
    }

    public function testMergeCellsHideBehaviour(): void
    {
        $expectedResult = [
            [1.1, 2.2, 3.3],
            [4.4, 5.5, 9.9],
            [5.5, 7.7, 13.2],
        ];

        $mergeRange = 'A1:C3';
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray(self::$testDataRaw, null, 'A1', true);
        $worksheet->mergeCells($mergeRange, Worksheet::MERGE_CELL_CONTENT_HIDE);

        $mergeResult = $worksheet->toArray(null, true, false, false);
        self::assertSame($expectedResult, $mergeResult);
        $spreadsheet->disconnectWorksheets();
    }

    public function testMergeCellsHideBehaviourFormatted(): void
    {
        $expectedResult = [
            ['1960-12-19', '2022-09-15'],
        ];

        $mergeRange = 'A1:B1';
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray($this->testDataFormatted, null, 'A1', true);
        $worksheet->getStyle($mergeRange)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $worksheet->mergeCells($mergeRange, Worksheet::MERGE_CELL_CONTENT_HIDE);

        $mergeResult = $worksheet->toArray(null, true, true, false);
        self::assertSame($expectedResult, $mergeResult);
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * @dataProvider mergeCellsMergeBehaviourProvider
     */
    public function testMergeCellsMergeBehaviour(array $testData, string $mergeRange, array $expectedResult): void
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray($testData, null, 'A1', true);
        // Force a precalculation to populate the calculation cache, so that we can verify that it is being cleared
        $worksheet->toArray();
        $worksheet->mergeCells($mergeRange, Worksheet::MERGE_CELL_CONTENT_MERGE);

        $mergeResult = $worksheet->toArray(null, true, true, false);
        self::assertSame($expectedResult, $mergeResult);
        $spreadsheet->disconnectWorksheets();
    }

    public static function mergeCellsMergeBehaviourProvider(): array
    {
        return [
            'With Calculated Values' => [
                self::$testDataRaw,
                'A1:C3',
                [
                    ['1.1 2.2 1.1 4.4 5.5 0 1.1 0 0', null, null],
                    [null, null, null],
                    [null, null, null],
                ],
            ],
            'With Empty Cells' => [
                [
                    [1, '', 2],
                    [null, 3, null],
                    [4, null, 5],
                ],
                'A1:C3',
                [
                    ['1 2 3 4 5', null, null],
                    [null, null, null],
                    [null, null, null],
                ],
            ],
            [
                [
                    [12, '=5+1', '=A1/A2'],
                ],
                'A1:C1',
                [
                    ['12 6 #DIV/0!', null, null],
                ],
            ],
        ];
    }

    public function testMergeCellsMergeBehaviourFormatted(): void
    {
        $expectedResult = [
            ['1960-12-19 2022-09-15', null],
        ];

        $mergeRange = 'A1:B1';
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->fromArray($this->testDataFormatted, null, 'A1', true);
        $worksheet->getStyle($mergeRange)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        $worksheet->mergeCells($mergeRange, Worksheet::MERGE_CELL_CONTENT_MERGE);

        $mergeResult = $worksheet->toArray(null, true, true, false);
        self::assertSame($expectedResult, $mergeResult);
        $spreadsheet->disconnectWorksheets();
    }
}
