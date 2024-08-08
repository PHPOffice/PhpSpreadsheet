<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
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
        $filename = 'tests/data/Reader/Gnumeric/ArrayFormulaTest.gnumeric';
        $reader = new Gnumeric();
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $cell = $worksheet->getCell($cellAddress);
        self::assertSame(DataType::TYPE_FORMULA, $cell->getDataType());
        if (is_array($expectedValue)) {
            self::assertSame(['t' => 'array', 'ref' => $expectedRange], $cell->getFormulaAttributes());
        } else {
            self::assertEmpty($cell->getFormulaAttributes());
        }
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
                'D1',
                'D1:E2',
                '=A1:B1*A1:A2',
                [[4, 6], [8, 12]],
            ],
            [
                'G1',
                'G1:J1',
                '=SIN({-1,0,1,2})',
                [[-0.8414709848078965, 0.0, 0.8414709848078965, 0.9092974268256817]],
            ],
            [
                'G3',
                'G3:G3',
                '=MAX(SIN({-1,0,1,2}))',
                0.9092974268256817,
            ],
            [
                'D4',
                'D4:E5',
                '=A4:B4*A4:A5',
                [[9, 12], [15, 20]],
            ],
            [
                'D7',
                'D7:E8',
                '=A7:B7*A7:A8',
                [[16, 20], [24, 30]],
            ],
            [
                'D10',
                'D10:E11',
                '=A10:B10*A10:A11',
                [[25, 30], [35, 42]],
            ],
            [
                'D13',
                'D13:E14',
                '=A13:B13*A13:A14',
                [[36, 42], [48, 56]],
            ],
        ];
    }
}
