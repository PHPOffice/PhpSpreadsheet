<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class BitLShiftTest extends TestCase
{
    /**
     * @dataProvider providerBITLSHIFT
     *
     * @param mixed $expectedResult
     */
    public function testBITLSHIFT($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 8);
        $sheet->getCell('A1')->setValue("=BITLSHIFT($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITLSHIFT(): array
    {
        return require 'tests/data/Calculation/Engineering/BITLSHIFT.php';
    }

    /**
     * @dataProvider providerBitLShiftArray
     */
    public function testBitLShiftArray(array $expectedResult, string $number, string $bits): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITLSHIFT({$number}, {$bits})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBitLShiftArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [14, 28, 56, 112, 224],
                    [16, 32, 64, 128, 256],
                    [18, 36, 72, 144, 288],
                ],
                '{7; 8; 9}',
                '{1, 2, 3, 4, 5}',
            ],
        ];
    }
}
