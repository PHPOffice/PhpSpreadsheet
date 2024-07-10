<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PHPUnit\Framework\TestCase;

class ArrayFormula2Test extends TestCase
{
    /**
     * @dataProvider arrayFormulaReaderProvider
     */
    public function testArrayFormulaReader(
        string $cellAddress,
        string $expectedRange,
        string $expectedFormula,
        array $expectedValue
    ): void {
        $filename = 'tests/data/Reader/Gnumeric/ArrayFormulaTest2.gnumeric';
        $reader = new Gnumeric();
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
        self::assertSame($expectedValue, $worksheet->rangeToArray($expectedRange, formatData: false, reduceArrays: true));
        $spreadsheet->disconnectWorksheets();
    }

    public static function arrayFormulaReaderProvider(): array
    {
        return [
            [
                'D1',
                'D1:E2',
                '=MMULT(A1:B2,A4:B5)',
                [[21, 26], [37, 46]],
            ],
            [
                'G1',
                'G1:J1',
                '=SIN({-1,0,1,2})',
                [[-0.8414709848078965, 0.0, 0.8414709848078965, 0.9092974268256817]],
            ],
            [
                'D4',
                'D4:E5',
                '=MMULT(A7:B8,A10:B11)',
                [[55, 64], [79, 92]],
            ],
        ];
    }
}
