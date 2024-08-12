<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class ArrayFormulaTest extends TestCase
{
    /**
     * @dataProvider arrayFormulaReaderProvider
     */
    public function testArrayFormulaReader(
        string $cellAddress,
        string $expectedRange,
        string $expectedFormula,
        array|float $expectedValue
    ): void {
        $filename = 'tests/data/Reader/Ods/ArrayFormulaTest.ods';
        $reader = new Ods();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $cell = $worksheet->getCell($cellAddress);
        self::assertSame(DataType::TYPE_FORMULA, $cell->getDataType());
        self::assertSame(['t' => 'array', 'ref' => $expectedRange], $cell->getFormulaAttributes());
        self::assertSame($expectedFormula, strtoupper($cell->getValue()));
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        $worksheet->calculateArrays();
        $cell = $worksheet->getCell($cellAddress);
        self::assertSame($expectedValue, $cell->getCalculatedValue());
        if (is_array($expectedValue)) {
            self::assertSame($expectedValue, $worksheet->rangeToArray($expectedRange, formatData: false, reduceArrays: true));
        } else {
            self::assertSame([[$expectedValue]], $worksheet->rangeToArray($expectedRange, formatData: false, reduceArrays: true));
        }
        $spreadsheet->disconnectWorksheets();
    }

    public static function arrayFormulaReaderProvider(): array
    {
        return [
            [
                'B2',
                'B2:C3',
                '={2,3}*{4;5}',
                [[8, 12], [10, 15]],
            ],
            [
                'E1',
                'E1:H1',
                '=SIN({-1,0,1,2})',
                [[-0.8414709848078965, 0.0, 0.8414709848078965, 0.9092974268256817]],
            ],
            [
                'E3',
                'E3:E3',
                '=MAX(SIN({-1,0,1,2}))',
                0.9092974268256817,
            ],
            [
                'D5',
                'D5:E6',
                '=A5:B5*A5:A6',
                [[4, 6], [8, 12]],
            ],
            [
                'D8',
                'D8:E9',
                '=A8:B8*A8:A9',
                [[9, 12], [15, 20]],
            ],
            [
                'D11',
                'D11:E12',
                '=A11:B11*A11:A12',
                [[16, 20], [24, 30]],
            ],
            [
                'D14',
                'D14:E15',
                '=A14:B14*A14:A15',
                [[25, 30], [35, 42]],
            ],
        ];
    }
}
