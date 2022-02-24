<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class BitXorTest extends TestCase
{
    /**
     * @dataProvider providerBITXOR
     *
     * @param mixed $expectedResult
     */
    public function testBITXOR($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 8);
        $sheet->getCell('A1')->setValue("=BITXOR($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITXOR(): array
    {
        return require 'tests/data/Calculation/Engineering/BITXOR.php';
    }

    /**
     * @dataProvider providerBitXorArray
     */
    public function testBitXorArray(array $expectedResult, string $number1, string $number2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITXOR({$number1}, {$number2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBitXorArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [4, 11, 10],
                    [3, 12, 13],
                    [2, 13, 12],
                ],
                '{7, 8, 9}',
                '{3; 4; 5}',
            ],
        ];
    }
}
