<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class BitAndTest extends TestCase
{
    /**
     * @dataProvider providerBITAND
     *
     * @param mixed $expectedResult
     */
    public function testBITAND($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 24);
        $sheet->getCell('A1')->setValue("=BITAND($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITAND(): array
    {
        return require 'tests/data/Calculation/Engineering/BITAND.php';
    }

    /**
     * @dataProvider providerBitAndArray
     */
    public function testBitAndArray(array $expectedResult, string $number1, string $number2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITAND({$number1}, {$number2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBitAndArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [3, 0, 1],
                    [4, 0, 0],
                    [5, 0, 1],
                ],
                '{7, 8, 9}',
                '{3; 4; 5}',
            ],
        ];
    }
}
