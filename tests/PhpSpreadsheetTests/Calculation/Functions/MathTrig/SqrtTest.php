<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalcExp;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class SqrtTest extends TestCase
{
    /**
     * @dataProvider providerSQRT
     *
     * @param mixed $expectedResult
     * @param mixed $val
     */
    public function testSQRT($expectedResult, $val = null): void
    {
        if ($val === null) {
            $this->expectException(CalcExp::class);
            $formula = '=SQRT()';
        } else {
            $formula = "=SQRT($val)";
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getCell('A1')->setValue($formula);
        $result = $sheet->getCell('A1')->getCalculatedValue();
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerSqrt()
    {
        return require 'tests/data/Calculation/MathTrig/SQRT.php';
    }
}
