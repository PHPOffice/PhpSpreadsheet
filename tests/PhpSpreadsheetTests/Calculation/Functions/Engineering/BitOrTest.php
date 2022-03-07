<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class BitOrTest extends TestCase
{
    /**
     * @dataProvider providerBITOR
     *
     * @param mixed $expectedResult
     */
    public function testBITOR($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 8);
        $sheet->getCell('A1')->setValue("=BITOR($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITOR(): array
    {
        return require 'tests/data/Calculation/Engineering/BITOR.php';
    }

    /**
     * @dataProvider providerBitOrArray
     */
    public function testBitOrArray(array $expectedResult, string $number1, string $number2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITOR({$number1}, {$number2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBitOrArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [7, 11, 11],
                    [7, 12, 13],
                    [7, 13, 13],
                ],
                '{7, 8, 9}',
                '{3; 4; 5}',
            ],
        ];
    }
}
