<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class BitRShiftTest extends TestCase
{
    /**
     * @dataProvider providerBITRSHIFT
     *
     * @param mixed $expectedResult
     */
    public function testBITRSHIFT($expectedResult, string $formula): void
    {
        if ($expectedResult === 'exception') {
            $this->expectException(CalcExp::class);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A2', 8);
        $sheet->getCell('A1')->setValue("=BITRSHIFT($formula)");
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITRSHIFT(): array
    {
        return require 'tests/data/Calculation/Engineering/BITRSHIFT.php';
    }

    /**
     * @dataProvider providerBitRShiftArray
     */
    public function testBitRShiftArray(array $expectedResult, string $number, string $bits): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BITRSHIFT({$number}, {$bits})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBitRShiftArray(): array
    {
        return [
            'row/column vector' => [
                [
                    [31, 15, 7, 3, 1],
                    [32, 16, 8, 4, 2],
                    [37, 18, 9, 4, 2],
                ],
                '{63; 64; 75}',
                '{1, 2, 3, 4, 5}',
            ],
        ];
    }
}
